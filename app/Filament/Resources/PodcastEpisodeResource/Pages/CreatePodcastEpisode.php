<?php

namespace App\Filament\Resources\PodcastEpisodeResource\Pages;

use App\Filament\Resources\PodcastEpisodeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePodcastEpisode extends CreateRecord {
    protected static string $resource = PodcastEpisodeResource::class;
}
