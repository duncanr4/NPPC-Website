<?php

namespace App\Filament\Resources\CalendarEntryResource\Pages;

use App\Filament\Resources\CalendarEntryResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCalendarEntry extends CreateRecord {
    protected static string $resource = CalendarEntryResource::class;
}
