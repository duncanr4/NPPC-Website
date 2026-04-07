<?php

namespace App\Filament\Resources\PodcastEpisodeResource\Pages;

use App\Filament\Resources\PodcastEpisodeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPodcastEpisode extends EditRecord {
    protected static string $resource = PodcastEpisodeResource::class;

    protected function getHeaderActions(): array {
        return [Actions\DeleteAction::make()];
    }
}
