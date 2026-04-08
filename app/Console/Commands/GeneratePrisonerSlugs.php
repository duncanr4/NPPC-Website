<?php

namespace App\Console\Commands;

use App\Models\Prisoner;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GeneratePrisonerSlugs extends Command {
    protected $signature = 'prisoners:generate-slugs {--dry-run : Preview without saving}';
    protected $description = 'Generate URL slugs for all prisoners from their names';

    public function handle(): int {
        $dryRun = $this->option('dry-run');
        $prisoners = Prisoner::all();
        $usedSlugs = [];
        $updated = 0;

        foreach ($prisoners as $prisoner) {
            $name = trim($prisoner->name);
            if (! $name) {
                continue;
            }

            // Base slug from full name
            $baseSlug = Str::slug($name);

            // If duplicate, try with middle name
            if (in_array($baseSlug, $usedSlugs)) {
                if ($prisoner->middle_name) {
                    $baseSlug = Str::slug($prisoner->first_name.' '.$prisoner->middle_name.' '.$prisoner->last_name);
                } elseif ($prisoner->aka) {
                    $baseSlug = Str::slug($prisoner->aka);
                }
            }

            // If still duplicate, append number
            $slug = $baseSlug;
            $counter = 1;
            while (in_array($slug, $usedSlugs)) {
                $counter++;
                $slug = $baseSlug.'-'.$counter;
            }

            $usedSlugs[] = $slug;

            if ($dryRun) {
                $this->line(sprintf('  %-35s → %s', $name, $slug));
            } else {
                $prisoner->slug = $slug;
                $prisoner->save();
            }

            $updated++;
        }

        if ($dryRun) {
            $this->warn("{$updated} slugs would be generated (dry run).");
        } else {
            $this->info("{$updated} prisoner slugs generated.");
        }

        return self::SUCCESS;
    }
}
