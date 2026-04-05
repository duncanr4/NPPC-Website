<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HistoryEraResource\Pages;
use App\Filament\Resources\HistoryEraResource\RelationManagers;
use App\Models\HistoryEra;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class HistoryEraResource extends Resource {
    protected static ?string $model = HistoryEra::class;
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationGroup = 'Content';
    protected static ?string $recordTitleAttribute = 'title';
    protected static ?string $modelLabel = 'History Era';

    public static function form(Form $form): Form {
        return $form
            ->schema([
                Forms\Components\Section::make('Era Details')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('nav_label')
                            ->required()
                            ->maxLength(50)
                            ->helperText('Short label shown in the era navigation bar (e.g. "1700s")'),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('tag_line')
                            ->maxLength(255)
                            ->helperText('Small tag above the heading (e.g. "The 18th Century")'),
                        Forms\Components\TextInput::make('heading')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->rows(3),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Visual Panel')
                    ->schema([
                        Forms\Components\TextInput::make('bg_class')
                            ->default('vbg-1700')
                            ->maxLength(50)
                            ->helperText('CSS background class for the visual panel'),
                        Forms\Components\TextInput::make('caption_era')
                            ->required()
                            ->maxLength(50)
                            ->helperText('Large text in the visual caption (e.g. "1700s")'),
                        Forms\Components\TextInput::make('caption_label')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Subtitle in the visual caption'),
                    ])
                    ->columns(3),

                Forms\Components\TextInput::make('sort_order')
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nav_label')
                    ->label('Era')
                    ->sortable(),
                Tables\Columns\TextColumn::make('heading')
                    ->limit(50),
                Tables\Columns\TextColumn::make('topics_count')
                    ->counts('topics')
                    ->label('Topics'),
                Tables\Columns\TextColumn::make('sort_order')
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array {
        return [
            RelationManagers\TopicsRelationManager::class,
        ];
    }

    public static function getPages(): array {
        return [
            'index'  => Pages\ListHistoryEras::route('/'),
            'create' => Pages\CreateHistoryEra::route('/create'),
            'edit'   => Pages\EditHistoryEra::route('/{record}/edit'),
        ];
    }
}
