<?php

namespace App\Console\Commands;

use App\Models\Prisoner;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DownloadMissingPhotos extends Command {
    protected $signature = 'prisoners:download-photos
                            {--url=https://marisam-airtable.patrickdeamorim.workers.dev/ : The Airtable proxy URL}
                            {--overwrite : Re-download even if photo already exists}
                            {--dry-run : Preview without downloading}';

    protected $description = 'Download missing prisoner photos from the Airtable CDN';

    public function handle(): int {
        $url = $this->option('url');
        $overwrite = $this->option('overwrite');
        $dryRun = $this->option('dry-run');

        $this->info('Fetching prisoner data from Airtable proxy...');

        $response = Http::timeout(60)->get($url);

        if (! $response->successful()) {
            $this->error("Failed to fetch data: HTTP {$response->status()}");

            return self::FAILURE;
        }

        $records = $response->json();
        $this->info(count($records).' records from Airtable.');

        // Build lookup of Airtable records by normalized name
        $airtableLookup = [];
        foreach ($records as $record) {
            $name = $record['name'] ?? '';
            if (! $name || empty($record['Photo'])) {
                continue;
            }
            $normalized = $this->normalize($name);
            $airtableLookup[$normalized] = [
                'name'  => $name,
                'photo' => $record['Photo'],
                'aka'   => $record['AKA'] ?? null,
            ];
            // Also index by AKA
            if (! empty($record['AKA'])) {
                $airtableLookup[$this->normalize($record['AKA'])] = $airtableLookup[$normalized];
            }
        }

        $this->info(count($airtableLookup).' Airtable records have photos.');

        // Find prisoners needing photos
        $query = Prisoner::query();
        if (! $overwrite) {
            $query->where(function ($q) {
                $q->whereNull('photo')->orWhere('photo', '');
            });
        }
        $prisoners = $query->get();

        if ($prisoners->isEmpty()) {
            $this->info('All prisoners already have photos!');

            return self::SUCCESS;
        }

        $this->info($prisoners->count().' prisoners need photos.');
        $this->newLine();

        Storage::disk('public')->makeDirectory('prisoners');

        $matched = [];
        $unmatched = [];

        foreach ($prisoners as $prisoner) {
            $normalized = $this->normalize($prisoner->name);
            $match = $airtableLookup[$normalized] ?? null;

            // Try AKA
            if (! $match && $prisoner->aka) {
                $match = $airtableLookup[$this->normalize($prisoner->aka)] ?? null;
            }

            // Try fuzzy match
            if (! $match) {
                $match = $this->fuzzyMatch($normalized, $airtableLookup);
            }

            if ($match) {
                $matched[] = [
                    'prisoner'  => $prisoner,
                    'photo_url' => $match['photo'],
                    'matched'   => $match['name'],
                ];
            } else {
                $unmatched[] = $prisoner->name;
            }
        }

        // Report
        $this->info('Matched: '.count($matched));
        if (! empty($unmatched)) {
            $this->warn('Unmatched: '.count($unmatched));
            if ($this->getOutput()->isVerbose()) {
                foreach ($unmatched as $name) {
                    $this->line("  - {$name}");
                }
            }
        }

        if (empty($matched)) {
            $this->warn('No matches found to download.');

            return self::SUCCESS;
        }

        if ($dryRun) {
            $this->table(
                ['Prisoner', 'Matched To', 'Photo URL'],
                array_map(fn ($m) => [
                    $m['prisoner']->name,
                    $m['matched'],
                    strlen($m['photo_url']) > 60 ? substr($m['photo_url'], 0, 57).'...' : $m['photo_url'],
                ], $matched)
            );
            $this->warn('Dry run — no photos downloaded.');

            return self::SUCCESS;
        }

        $this->newLine();
        $bar = $this->output->createProgressBar(count($matched));
        $downloaded = 0;
        $failed = 0;

        foreach ($matched as $match) {
            $prisoner = $match['prisoner'];
            $photoUrl = $match['photo_url'];

            try {
                $imageResponse = Http::timeout(30)->get($photoUrl);

                if (! $imageResponse->successful()) {
                    $failed++;
                    $bar->advance();
                    continue;
                }

                $contentType = $imageResponse->header('Content-Type');
                $ext = $this->extensionFromMime($contentType);
                $filename = 'prisoners/'.Str::slug($prisoner->name).'-'.$prisoner->id.'.'.$ext;

                Storage::disk('public')->put($filename, $imageResponse->body());

                $prisoner->photo = $filename;
                $prisoner->save();

                $downloaded++;
            } catch (\Exception $e) {
                $failed++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Downloaded: {$downloaded}");
        if ($failed > 0) {
            $this->warn("Failed: {$failed}");
        }

        return self::SUCCESS;
    }

    private function normalize(string $name): string {
        return strtolower(trim(preg_replace('/\s+/', ' ', preg_replace('/[^a-zA-Z0-9\s]/', '', $name))));
    }

    private function fuzzyMatch(string $normalized, array $lookup): ?array {
        $bestMatch = null;
        $bestScore = 0;

        foreach ($lookup as $key => $entry) {
            // Substring containment
            if (str_contains($key, $normalized) || str_contains($normalized, $key)) {
                return $entry;
            }

            similar_text($normalized, $key, $percent);
            if ($percent > $bestScore) {
                $bestScore = $percent;
                $bestMatch = $entry;
            }
        }

        return $bestScore >= 75 ? $bestMatch : null;
    }

    private function extensionFromMime(?string $mime): string {
        return match (true) {
            str_contains($mime ?? '', 'png')  => 'png',
            str_contains($mime ?? '', 'gif')  => 'gif',
            str_contains($mime ?? '', 'webp') => 'webp',
            default                           => 'jpg',
        };
    }
}
