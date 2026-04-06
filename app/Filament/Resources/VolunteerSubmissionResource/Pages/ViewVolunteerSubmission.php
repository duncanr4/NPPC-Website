<?php

namespace App\Filament\Resources\VolunteerSubmissionResource\Pages;

use App\Filament\Resources\VolunteerSubmissionResource;
use Filament\Resources\Pages\ViewRecord;

class ViewVolunteerSubmission extends ViewRecord {
    protected static string $resource = VolunteerSubmissionResource::class;

    protected function mutateFormDataBeforeFill(array $data): array {
        if ($this->record->status === 'new') {
            $this->record->update(['status' => 'read']);
        }

        return $data;
    }
}
