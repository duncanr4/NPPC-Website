<?php

namespace App\Console\Commands;

use App\Models\Prisoner;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MatchPrisonerPhotos extends Command {
    protected $signature = 'prisoners:match-photos
                            {path : Directory containing photo files}
                            {--dry-run : Preview matches without copying files}
                            {--threshold=70 : Minimum similarity percentage for fuzzy matching (0-100)}';

    protected $description = 'Match local photo files to prisoners by name and import them';

    public function handle(): int {
        $path = $this->argument('path');
        $dryRun = $this->option('dry-run');
        $threshold = (int) $this->option('threshold');

        if (! is_dir($path)) {
            $this->error("Directory not found: {$path}");

            return self::FAILURE;
        }

        // Ensure storage directory exists
        Storage::disk('public')->makeDirectory('prisoners');

        // Scan for image files
        $extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'tiff', 'tif'];
        $files = [];
        foreach (scandir($path) as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (in_array($ext, $extensions)) {
                $files[] = $file;
            }
        }

        if (empty($files)) {
            $this->error("No image files found in: {$path}");

            return self::FAILURE;
        }

        $this->info(count($files).' image files found.');

        // Load all prisoners
        $prisoners = Prisoner::all();

        if ($prisoners->isEmpty()) {
            $this->error('No prisoners in database. Run airtable:import first.');

            return self::FAILURE;
        }

        $this->info($prisoners->count().' prisoners in database.');
        $this->newLine();

        // Build name lookup: normalize prisoner names for matching
        $prisonerIndex = [];
        foreach ($prisoners as $prisoner) {
            $prisonerIndex[] = [
                'model'           => $prisoner,
                'name'            => $prisoner->name,
                'normalized'      => $this->normalize($prisoner->name),
                'aka_normalized'  => $prisoner->aka ? $this->normalize($prisoner->aka) : null,
                'first_last'      => $this->normalize(trim(($prisoner->first_name ?? '').' '.($prisoner->last_name ?? ''))),
            ];
        }

        $matched = [];
        $unmatched = [];
        $ambiguous = [];

        foreach ($files as $file) {
            $fileBaseName = pathinfo($file, PATHINFO_FILENAME);
            $normalizedFile = $this->normalize($fileBaseName);

            $bestMatch = null;
            $bestScore = 0;
            $tiedMatches = [];

            foreach ($prisonerIndex as $entry) {
                // Try exact normalized match first
                if ($normalizedFile === $entry['normalized']) {
                    $bestMatch = $entry;
                    $bestScore = 100;
                    $tiedMatches = [];
                    break;
                }

                // Try AKA match
                if ($entry['aka_normalized'] && $normalizedFile === $entry['aka_normalized']) {
                    $bestMatch = $entry;
                    $bestScore = 100;
                    $tiedMatches = [];
                    break;
                }

                // Fuzzy match on full name
                $score = $this->similarityScore($normalizedFile, $entry['normalized']);

                // Also try AKA
                if ($entry['aka_normalized']) {
                    $akaScore = $this->similarityScore($normalizedFile, $entry['aka_normalized']);
                    $score = max($score, $akaScore);
                }

                // Also try first+last
                if ($entry['first_last'] && strlen($entry['first_last']) > 1) {
                    $flScore = $this->similarityScore($normalizedFile, $entry['first_last']);
                    $score = max($score, $flScore);
                }

                // Also check if filename contains the prisoner name or vice versa
                if (str_contains($normalizedFile, $entry['normalized']) || str_contains($entry['normalized'], $normalizedFile)) {
                    $score = max($score, 85);
                }

                if ($score > $bestScore) {
                    $bestScore = $score;
                    $bestMatch = $entry;
                    $tiedMatches = [];
                } elseif ($score === $bestScore && $score >= $threshold && $bestMatch) {
                    $tiedMatches[] = $entry;
                }
            }

            if ($bestScore >= $threshold && empty($tiedMatches)) {
                $matched[] = [
                    'file'     => $file,
                    'prisoner' => $bestMatch['model'],
                    'name'     => $bestMatch['name'],
                    'score'    => $bestScore,
                ];
            } elseif ($bestScore >= $threshold && ! empty($tiedMatches)) {
                $ambiguous[] = [
                    'file'    => $file,
                    'matches' => array_merge([$bestMatch['name']], array_map(fn ($m) => $m['name'], $tiedMatches)),
                    'score'   => $bestScore,
                ];
            } else {
                $unmatched[] = [
                    'file'       => $file,
                    'best_guess' => $bestMatch ? $bestMatch['name'] : '-',
                    'score'      => $bestScore,
                ];
            }
        }

        // Show matched
        if (! empty($matched)) {
            $this->info('Matched: '.count($matched));
            $this->table(
                ['File', 'Prisoner', 'Score'],
                array_map(fn ($m) => [$m['file'], $m['name'], $m['score'].'%'], $matched)
            );
        }

        // Show ambiguous
        if (! empty($ambiguous)) {
            $this->warn('Ambiguous (multiple matches): '.count($ambiguous));
            $this->table(
                ['File', 'Possible Matches', 'Score'],
                array_map(fn ($a) => [$a['file'], implode(', ', $a['matches']), $a['score'].'%'], $ambiguous)
            );
        }

        // Show unmatched
        if (! empty($unmatched)) {
            $this->warn('Unmatched: '.count($unmatched));
            $this->table(
                ['File', 'Best Guess', 'Score'],
                array_map(fn ($u) => [$u['file'], $u['best_guess'], $u['score'].'%'], $unmatched)
            );
        }

        if ($dryRun) {
            $this->newLine();
            $this->warn('Dry run — no files were copied.');

            return self::SUCCESS;
        }

        if (empty($matched)) {
            $this->warn('No matches found. Try lowering --threshold (currently '.$threshold.').');

            return self::SUCCESS;
        }

        if (! $this->confirm('Proceed with importing '.count($matched).' matched photos?')) {
            return self::SUCCESS;
        }

        $imported = 0;

        foreach ($matched as $match) {
            $file = $match['file'];
            $prisoner = $match['prisoner'];
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            $storagePath = 'prisoners/'.Str::slug($prisoner->name).'-'.$prisoner->id.'.'.$ext;

            // Copy file to storage
            $sourcePath = rtrim($path, '/\\').DIRECTORY_SEPARATOR.$file;
            Storage::disk('public')->put($storagePath, file_get_contents($sourcePath));

            // Update prisoner record
            $prisoner->photo = $storagePath;
            $prisoner->save();

            $imported++;
        }

        $this->newLine();
        $this->info("{$imported} photos imported successfully.");

        return self::SUCCESS;
    }

    private function normalize(string $name): string {
        // Remove file-name artifacts: underscores, hyphens, dots → spaces
        $name = str_replace(['_', '-', '.'], ' ', $name);
        // Remove common suffixes
        $name = preg_replace('/\s*(headshot|photo|pic|portrait|mugshot|profile|image|img)\s*/i', ' ', $name);
        // Remove parenthesized content
        $name = preg_replace('/\([^)]*\)/', '', $name);
        // Collapse whitespace, trim, lowercase
        $name = strtolower(trim(preg_replace('/\s+/', ' ', $name)));

        return $name;
    }

    private function similarityScore(string $a, string $b): int {
        if (! $a || ! $b) {
            return 0;
        }

        // Try similar_text percentage
        similar_text($a, $b, $percent);

        return (int) round($percent);
    }
}
