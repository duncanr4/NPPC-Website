<?php

namespace App\Filament\Resources\PrisonerResource\Pages;

use App\Filament\Resources\PrisonerResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePrisoner extends CreateRecord {
    protected static string $resource = PrisonerResource::class;
}
