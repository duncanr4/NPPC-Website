<?php

namespace App\Filament\Resources\PodcastEpisodeResource\Pages;

use App\Filament\Resources\PodcastEpisodeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPodcastEpisodes extends ListRecords {
    protected static string $resource = PodcastEpisodeResource::class;

    protected function getHeaderActions(): array {
        return [Actions\CreateAction::make()];
    }
}
