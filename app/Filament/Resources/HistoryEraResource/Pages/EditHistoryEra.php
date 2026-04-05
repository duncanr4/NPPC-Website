<?php

namespace App\Filament\Resources\HistoryEraResource\Pages;

use App\Filament\Resources\HistoryEraResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHistoryEra extends EditRecord {
    protected static string $resource = HistoryEraResource::class;

    protected function getHeaderActions(): array {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
