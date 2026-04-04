<?php

namespace App\Filament\Resources\PrisonerCaseResource\Pages;

use App\Filament\Resources\PrisonerCaseResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPrisonerCase extends EditRecord {
    protected static string $resource = PrisonerCaseResource::class;

    protected function getHeaderActions(): array {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
