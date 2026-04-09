<?php

namespace App\Console\Commands;

use App\Models\CalendarEntry;
use App\Models\PrisonerCase;
use App\Models\Prisoner;
use Illuminate\Console\Command;

class GenerateCalendarEntries extends Command {
    protected $signature = 'calendar:generate {--dry-run : Preview without saving}';
    protected $description = 'Auto-generate calendar entries from prisoner case dates';

    public function handle(): int {
        $dryRun = $this->option('dry-run');
        $created = 0;
        $skipped = 0;

        $cases = PrisonerCase::with(['prisoner', 'institution'])->get();

        foreach ($cases as $case) {
            if (! $case->prisoner) continue;

            $events = [];

            if ($case->arrest_date) {
                $events[] = [
                    'date'  => $case->arrest_date,
                    'title' => $case->prisoner->name.' arrested',
                    'type'  => 'arrest',
                ];
            }

            if ($case->incarceration_date) {
                $events[] = [
                    'date'  => $case->incarceration_date,
                    'title' => $case->prisoner->name.' incarcerated'.($case->institution ? ' at '.$case->institution->name : ''),
                    'type'  => 'incarceration',
                ];
            }

            if ($case->sentenced_date) {
                $events[] = [
                    'date'  => $case->sentenced_date,
                    'title' => $case->prisoner->name.' sentenced'.($case->sentence ? ': '.$case->sentence : ''),
                    'type'  => 'sentencing',
                ];
            }

            if ($case->release_date) {
                $events[] = [
                    'date'  => $case->release_date,
                    'title' => $case->prisoner->name.' released',
                    'type'  => 'release',
                ];
            }

            if ($case->death_in_custody_date) {
                $events[] = [
                    'date'  => $case->death_in_custody_date,
                    'title' => $case->prisoner->name.' died in custody',
                    'type'  => 'death',
                ];
            }

            foreach ($events as $event) {
                $month = (int) $event['date']->format('n');
                $day = (int) $event['date']->format('j');
                $year = (int) $event['date']->format('Y');

                // Check if entry already exists for this month/day
                $exists = CalendarEntry::where('month', $month)
                    ->where('day', $day)
                    ->exists();

                if ($exists) {
                    $skipped++;
                    continue;
                }

                if ($dryRun) {
                    $this->line(sprintf('  %s %02d → %s (%d)', $event['date']->format('M'), $day, $event['title'], $year));
                } else {
                    CalendarEntry::create([
                        'month'       => $month,
                        'day'         => $day,
                        'year'        => $year,
                        'title'       => $event['title'],
                        'description' => null,
                        'published'   => true,
                    ]);
                }

                $created++;
            }
        }

        // Also generate from prisoner birthdates and death dates
        $prisoners = Prisoner::all();
        foreach ($prisoners as $prisoner) {
            if ($prisoner->birthdate) {
                $month = (int) $prisoner->birthdate->format('n');
                $day = (int) $prisoner->birthdate->format('j');
                $year = (int) $prisoner->birthdate->format('Y');

                if (! CalendarEntry::where('month', $month)->where('day', $day)->exists()) {
                    if ($dryRun) {
                        $this->line(sprintf('  %s %02d → %s born (%d)', $prisoner->birthdate->format('M'), $day, $prisoner->name, $year));
                    } else {
                        CalendarEntry::create([
                            'month'       => $month,
                            'day'         => $day,
                            'year'        => $year,
                            'title'       => $prisoner->name.' born',
                            'published'   => true,
                        ]);
                    }
                    $created++;
                } else {
                    $skipped++;
                }
            }
        }

        if ($dryRun) {
            $this->warn("{$created} entries would be created, {$skipped} skipped (slot taken).");
        } else {
            $this->info("{$created} entries created, {$skipped} skipped.");
        }

        return self::SUCCESS;
    }
}
