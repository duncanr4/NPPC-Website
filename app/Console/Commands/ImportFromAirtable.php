<?php

namespace App\Console\Commands;

use App\Models\Institution;
use App\Models\Prisoner;
use App\Models\PrisonerCase;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ImportFromAirtable extends Command {
    protected $signature = 'airtable:import
                            {--url=https://marisam-airtable.patrickdeamorim.workers.dev/ : The Airtable proxy URL}
                            {--dry-run : Preview without writing to database}';

    protected $description = 'Import prisoner data from the Airtable Cloudflare proxy into local database';

    public function handle(): int {
        $url = $this->option('url');
        $dryRun = $this->option('dry-run');

        $this->info("Fetching data from: {$url}");

        $response = Http::timeout(60)->get($url);

        if (! $response->successful()) {
            $this->error("Failed to fetch data: HTTP {$response->status()}");

            return self::FAILURE;
        }

        $records = $response->json();

        if (! is_array($records) || empty($records)) {
            $this->error('No records returned from Airtable proxy.');

            return self::FAILURE;
        }

        $this->info(count($records).' prisoners found.');

        if ($dryRun) {
            $this->warn('Dry run mode — no data will be written.');
            $this->table(
                ['Name', 'AKA', 'State', 'In Custody', 'Cases'],
                collect($records)->map(fn ($r) => [
                    $r['name'] ?? '-',
                    $r['AKA'] ?? '-',
                    $r['State'] ?? '-',
                    ($r['In Custody'] ?? false) ? 'Yes' : 'No',
                    count($r['cases'] ?? []),
                ])->toArray()
            );

            return self::SUCCESS;
        }

        $this->info('Importing...');
        $bar = $this->output->createProgressBar(count($records));

        // Track institutions we've already created (by name) to avoid duplicates
        $institutionCache = [];

        $prisonersCreated = 0;
        $casesCreated = 0;
        $institutionsCreated = 0;

        foreach ($records as $record) {
            // Create prisoner
            $prisoner = Prisoner::create([
                'name'                 => $record['name'] ?? '',
                'sort_order'           => $record['SortOrder'] ?? 0,
                'photo'                => null, // Photos are Airtable CDN URLs — stored as description note below
                'description'          => $record['Description'] ?? null,
                'years_in_prison'      => ! empty($record['Years Spent In Prison']) ? (int) $record['Years Spent In Prison'][0] : null,
                'state'                => $record['State'] ?? null,
                'address'              => $record['Address'] ?? null,
                'lat'                  => $record['latitude'] ?? null,
                'lng'                  => $record['longitude'] ?? null,
                'first_name'           => null,
                'middle_name'          => null,
                'last_name'            => null,
                'aka'                  => $record['AKA'] ?? null,
                'race'                 => $record['Race'] ?? null,
                'gender'               => $record['Gender'] ?? null,
                'birthdate'            => $this->parseDate($record['Birthdate'] ?? null),
                'death_date'           => $this->parseDate($record['Death date'] ?? null),
                'age'                  => is_numeric($record['Age'] ?? null) ? (int) $record['Age'] : null,
                'ideologies'           => $record['Ideologies'] ?? null,
                'era'                  => $record['Era'] ?? null,
                'affiliation'          => $record['Affiliation'] ?? null,
                'in_custody'           => (bool) ($record['In Custody'] ?? false),
                'released'             => (bool) ($record['Released'] ?? false),
                'in_exile'             => (bool) ($record['In Exile'] ?? false),
                'currently_in_exile'   => (bool) ($record['Currently in Exile'] ?? false),
                'imprisoned_or_exiled' => ($record['Imprisoned or Exiled'] ?? null) === 'T',
                'website'              => $record['Website'] ?? null,
                'twitter'              => $record['Twitter'] ?? null,
                'facebook'             => $record['Facebook'] ?? null,
                'instagram'            => null,
                'inmate_number'        => $record['inmateNumber'] ?? null,
                'awaiting_trial'       => (bool) ($record['Awaiting Trial'] ?? false),
            ]);

            $prisonersCreated++;

            // Store the Airtable photo URL in description as a note if photo exists
            if (! empty($record['Photo']) && $prisoner->description) {
                // Photo URLs are Airtable CDN — they'll need manual download or a separate migration
            }

            // Import cases
            foreach ($record['cases'] ?? [] as $caseData) {
                $institutionId = null;

                // Resolve institution
                $instNames = $caseData['Institution name'] ?? [];
                $instName = is_array($instNames) ? ($instNames[0] ?? null) : $instNames;

                if ($instName) {
                    if (isset($institutionCache[$instName])) {
                        $institutionId = $institutionCache[$instName];
                    } else {
                        $institution = Institution::create([
                            'name'             => $instName,
                            'city'             => is_array($caseData['Institution city'] ?? null) ? ($caseData['Institution city'][0] ?? null) : ($caseData['Institution city'] ?? null),
                            'state'            => $caseData['Institution state'] ?? null,
                            'security'         => is_array($caseData['Institution security'] ?? null) ? ($caseData['Institution security'][0] ?? null) : ($caseData['Institution security'] ?? null),
                            'mailing_address'  => $caseData['Mailing address'] ?? null,
                            'physical_address' => $caseData['Physical address'] ?? null,
                        ]);
                        $institutionId = $institution->id;
                        $institutionCache[$instName] = $institutionId;
                        $institutionsCreated++;
                    }
                }

                PrisonerCase::create([
                    'prisoner_id'           => $prisoner->id,
                    'institution_id'        => $institutionId,
                    'charges'               => is_array($caseData['Charges'] ?? null) ? implode("\n", $caseData['Charges']) : ($caseData['Charges'] ?? null),
                    'arrest_date'           => $this->parseDate($caseData['Arrest Date'] ?? null),
                    'indicted'              => $caseData['Indicted'] ?? null,
                    'convicted'             => $caseData['Convicted'] ?? null,
                    'plead'                 => $caseData['Plead'] ?? null,
                    'sentenced_date'        => $this->parseDate($caseData['Sentenced Date'] ?? null),
                    'incarceration_date'    => $this->parseDate($caseData['Incarceration Date'] ?? null),
                    'release_date'          => $this->parseDate($caseData['Release Date'] ?? null),
                    'death_in_custody_date' => null,
                    'in_exile_since'        => null,
                    'end_of_exile'          => null,
                    'prosecutor'            => $caseData['Prosecutor'] ?? null,
                    'judge'                 => $caseData['Judge'] ?? null,
                    'sentence'              => $caseData['Sentence'] ?? null,
                    'imprisoned_for_days'   => $record['imprisonedFor'] ?? null,
                    'in_exile_for_days'     => $record['inExileFor'] ?? null,
                ]);

                $casesCreated++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Import complete!");
        $this->table(
            ['Type', 'Created'],
            [
                ['Prisoners', $prisonersCreated],
                ['Cases', $casesCreated],
                ['Institutions', $institutionsCreated],
            ]
        );

        return self::SUCCESS;
    }

    private function parseDate(?string $value): ?string {
        if (! $value) {
            return null;
        }

        try {
            return \Carbon\Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception) {
            return null;
        }
    }
}
