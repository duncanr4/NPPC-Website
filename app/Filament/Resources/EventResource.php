<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventResource\Pages;
use App\Models\Event;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EventResource extends Resource {
    protected static ?string $model = Event::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'Content';
    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Form $form): Form {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('event_date')
                    ->required(),
                Forms\Components\TextInput::make('time')
                    ->placeholder('e.g. 6:00 PM - 9:00 PM')
                    ->maxLength(100),
                Forms\Components\TextInput::make('location')
                    ->placeholder('e.g. Virtual / New York, NY')
                    ->maxLength(255),
                Forms\Components\TextInput::make('event_url')
                    ->label('Event Link')
                    ->url()
                    ->placeholder('https://...')
                    ->maxLength(255),
                Forms\Components\TextInput::make('series')
                    ->placeholder('e.g. Speaker Series, Annual Events')
                    ->maxLength(255),
                Forms\Components\FileUpload::make('image')
                    ->image()
                    ->disk('public')
                    ->directory('events'),
                Forms\Components\Textarea::make('description')
                    ->rows(3)
                    ->helperText('Short description shown in the event listing'),
                \FilamentTiptapEditor\TiptapEditor::make('body')
                    ->label('Full Details')
                    ->profile('default')
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('published')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->disk('public')
                    ->circular(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('event_date')
                    ->date('M j, Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('location'),
                Tables\Columns\TextColumn::make('series')
                    ->badge(),
                Tables\Columns\IconColumn::make('published')
                    ->boolean(),
            ])
            ->defaultSort('event_date', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('published'),
                Tables\Filters\Filter::make('upcoming')
                    ->label('Upcoming')
                    ->query(fn ($query) => $query->where('event_date', '>=', now())),
            ])
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
            'index'  => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit'   => Pages\EditEvent::route('/{record}/edit'),
        ];
    }
}
