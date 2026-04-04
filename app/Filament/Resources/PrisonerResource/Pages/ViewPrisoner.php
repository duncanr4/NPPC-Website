<?php

namespace App\Filament\Resources\PrisonerResource\Pages;

use App\Filament\Resources\PrisonerResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPrisoner extends ViewRecord {
    protected static string $resource = PrisonerResource::class;

    protected function getHeaderActions(): array {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
