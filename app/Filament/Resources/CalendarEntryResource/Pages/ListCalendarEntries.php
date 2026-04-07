<?php

namespace App\Filament\Resources\CalendarEntryResource\Pages;

use App\Filament\Resources\CalendarEntryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCalendarEntries extends ListRecords {
    protected static string $resource = CalendarEntryResource::class;

    protected function getHeaderActions(): array {
        return [Actions\CreateAction::make()];
    }
}
