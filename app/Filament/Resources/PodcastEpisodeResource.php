<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PodcastEpisodeResource\Pages;
use App\Models\PodcastEpisode;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PodcastEpisodeResource extends Resource {
    protected static ?string $model = PodcastEpisode::class;
    protected static ?string $navigationIcon = 'heroicon-o-microphone';
    protected static ?string $navigationGroup = 'Content';
    protected static ?string $navigationLabel = 'Podcast';
    protected static ?string $modelLabel = 'Podcast Episode';

    public static function form(Form $form): Form {
        return $form
            ->schema([
                Forms\Components\Section::make('Episode Details')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('show_name')
                            ->placeholder('e.g. NPPC Podcast')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('episode_number')
                            ->numeric(),
                        Forms\Components\TextInput::make('duration')
                            ->placeholder('e.g. 46:30')
                            ->maxLength(20),
                        Forms\Components\Textarea::make('description')
                            ->rows(3),
                    ]),

                Forms\Components\Section::make('Audio Source')
                    ->description('Provide either an embed code from your podcast host (Buzzsprout, Spotify, etc.) OR a direct audio file URL.')
                    ->schema([
                        Forms\Components\Textarea::make('embed_code')
                            ->label('Embed Code (HTML)')
                            ->rows(4)
                            ->helperText('Paste the full embed code from your podcast provider (e.g. Buzzsprout, Spotify, Apple Podcasts). This takes priority over audio URL.'),
                        Forms\Components\TextInput::make('audio_url')
                            ->label('Direct Audio URL')
                            ->url()
                            ->placeholder('https://...mp3')
                            ->helperText('Direct link to an MP3/audio file. Used if no embed code is provided.'),
                    ]),

                Forms\Components\Section::make('Media & Links')
                    ->schema([
                        Forms\Components\FileUpload::make('cover_image')
                            ->image()
                            ->disk('public')
                            ->directory('podcast'),
                        Forms\Components\Select::make('prisoner_id')
                            ->label('Linked Prisoner')
                            ->relationship('prisoner', 'name')
                            ->nullable()
                            ->searchable()
                            ->preload()
                            ->helperText('Link this episode to a prisoner profile page'),
                    ]),

                Forms\Components\Toggle::make('published')
                    ->default(true),
                Forms\Components\TextInput::make('sort_order')
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover_image')
                    ->disk('public')
                    ->circular()
                    ->size(40),
                Tables\Columns\TextColumn::make('episode_number')
                    ->label('#')
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('show_name'),
                Tables\Columns\TextColumn::make('duration'),
                Tables\Columns\TextColumn::make('prisoner.name')
                    ->label('Linked Prisoner'),
                Tables\Columns\IconColumn::make('published')
                    ->boolean(),
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

    public static function getPages(): array {
        return [
            'index'  => Pages\ListPodcastEpisodes::route('/'),
            'create' => Pages\CreatePodcastEpisode::route('/create'),
            'edit'   => Pages\EditPodcastEpisode::route('/{record}/edit'),
        ];
    }
}
