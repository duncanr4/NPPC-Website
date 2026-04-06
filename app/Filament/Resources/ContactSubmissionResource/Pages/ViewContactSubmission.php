<?php

namespace App\Filament\Resources\ContactSubmissionResource\Pages;

use App\Filament\Resources\ContactSubmissionResource;
use Filament\Resources\Pages\ViewRecord;

class ViewContactSubmission extends ViewRecord {
    protected static string $resource = ContactSubmissionResource::class;

    protected function mutateFormDataBeforeFill(array $data): array {
        // Auto-mark as read when viewed
        if ($this->record->status === 'new') {
            $this->record->update(['status' => 'read']);
        }

        return $data;
    }
}
