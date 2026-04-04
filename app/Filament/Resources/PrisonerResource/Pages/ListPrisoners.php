<?php

namespace App\Filament\Resources\PrisonerResource\Pages;

use App\Filament\Resources\PrisonerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPrisoners extends ListRecords {
    protected static string $resource = PrisonerResource::class;

    protected function getHeaderActions(): array {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
