<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CalendarEntryResource\Pages;
use App\Models\CalendarEntry;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CalendarEntryResource extends Resource {
    protected static ?string $model = CalendarEntry::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationGroup = 'Content';
    protected static ?string $navigationLabel = 'Calendar';
    protected static ?string $modelLabel = 'Calendar Entry';

    public static function form(Form $form): Form {
        return $form
            ->schema([
                Forms\Components\Select::make('month')
                    ->options(array_combine(range(1, 12), [
                        'January', 'February', 'March', 'April', 'May', 'June',
                        'July', 'August', 'September', 'October', 'November', 'December',
                    ]))
                    ->required(),
                Forms\Components\TextInput::make('day')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(31)
                    ->required(),
                Forms\Components\TextInput::make('year')
                    ->numeric()
                    ->required()
                    ->helperText('The historical year of the event'),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('description')
                    ->rows(4)
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('image')
                    ->image()
                    ->disk('public')
                    ->directory('calendar'),
                Forms\Components\Toggle::make('published')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('month')
                    ->formatStateUsing(fn (int $state): string => date('F', mktime(0, 0, 0, $state, 1)))
                    ->sortable(),
                Tables\Columns\TextColumn::make('day')
                    ->sortable(),
                Tables\Columns\TextColumn::make('year')
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->limit(50),
                Tables\Columns\ImageColumn::make('image')
                    ->disk('public')
                    ->circular(),
                Tables\Columns\IconColumn::make('published')
                    ->boolean(),
            ])
            ->defaultSort('month')
            ->filters([
                Tables\Filters\SelectFilter::make('month')
                    ->options(array_combine(range(1, 12), [
                        'January', 'February', 'March', 'April', 'May', 'June',
                        'July', 'August', 'September', 'October', 'November', 'December',
                    ])),
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
            'index'  => Pages\ListCalendarEntries::route('/'),
            'create' => Pages\CreateCalendarEntry::route('/create'),
            'edit'   => Pages\EditCalendarEntry::route('/{record}/edit'),
        ];
    }
}
