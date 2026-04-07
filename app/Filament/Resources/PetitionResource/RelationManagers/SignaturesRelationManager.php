<?php

namespace App\Filament\Resources\PetitionResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class SignaturesRelationManager extends RelationManager {
    protected static string $relationship = 'signatures';
    protected static ?string $recordTitleAttribute = 'email';

    public function table(Table $table): Table {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('first_name'),
                Tables\Columns\TextColumn::make('last_name'),
                Tables\Columns\TextColumn::make('email')
                    ->copyable(),
                Tables\Columns\TextColumn::make('city'),
                Tables\Columns\TextColumn::make('state'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('M j, g:ia')
                    ->label('Signed')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
