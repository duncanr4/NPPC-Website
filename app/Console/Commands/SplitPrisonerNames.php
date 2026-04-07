<?php

namespace App\Console\Commands;

use App\Models\Prisoner;
use Illuminate\Console\Command;

class SplitPrisonerNames extends Command {
    protected $signature = 'prisoners:split-names
                            {--dry-run : Preview without saving}
                            {--overwrite : Overwrite existing first/last/middle names}';

    protected $description = 'Auto-generate first_name, middle_name, and last_name from the name field';

    public function handle(): int {
        $dryRun = $this->option('dry-run');
        $overwrite = $this->option('overwrite');

        $query = Prisoner::query();

        if (! $overwrite) {
            $query->where(function ($q) {
                $q->whereNull('first_name')
                  ->orWhere('first_name', '');
            });
        }

        $prisoners = $query->get();
        $this->info($prisoners->count().' prisoners to process.');

        if ($prisoners->isEmpty()) {
            $this->info('All prisoners already have name fields populated. Use --overwrite to re-split.');

            return self::SUCCESS;
        }

        $updated = 0;

        foreach ($prisoners as $prisoner) {
            $name = trim($prisoner->name);

            if (! $name) {
                continue;
            }

            // Clean up extra spaces
            $name = preg_replace('/\s+/', ' ', $name);

            $parts = explode(' ', $name);
            $firstName = '';
            $middleName = '';
            $lastName = '';

            if (count($parts) === 1) {
                $firstName = $parts[0];
            } elseif (count($parts) === 2) {
                $firstName = $parts[0];
                $lastName = $parts[1];
            } elseif (count($parts) === 3) {
                $firstName = $parts[0];
                $middleName = $parts[1];
                $lastName = $parts[2];
            } else {
                // 4+ parts: first word is first name, last word is last name, middle is everything else
                $firstName = array_shift($parts);
                $lastName = array_pop($parts);
                $middleName = implode(' ', $parts);
            }

            if ($dryRun) {
                $this->line(sprintf(
                    '  %-30s → first: %-15s middle: %-15s last: %-15s',
                    $name,
                    $firstName,
                    $middleName ?: '-',
                    $lastName ?: '-'
                ));
            } else {
                $prisoner->first_name = $firstName ?: null;
                $prisoner->middle_name = $middleName ?: null;
                $prisoner->last_name = $lastName ?: null;
                $prisoner->save();
            }

            $updated++;
        }

        if ($dryRun) {
            $this->newLine();
            $this->warn("Dry run — {$updated} prisoners would be updated.");
        } else {
            $this->info("{$updated} prisoners updated.");
        }

        return self::SUCCESS;
    }
}
