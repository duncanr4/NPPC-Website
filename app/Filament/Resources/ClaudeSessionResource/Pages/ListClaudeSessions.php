<?php

namespace App\Filament\Resources\ClaudeSessionResource\Pages;

use App\Filament\Resources\ClaudeSessionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListClaudeSessions extends ListRecords {
    protected static string $resource = ClaudeSessionResource::class;

    protected function getHeaderActions(): array {
        return [
            Actions\Action::make('create')
                ->label('New Claude Session')
                ->icon('heroicon-o-plus')
                ->url(ClaudeSessionResource::getUrl('create')),
        ];
    }
}
