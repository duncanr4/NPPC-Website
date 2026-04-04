<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrisonerResource\Pages;
use App\Filament\Resources\PrisonerResource\RelationManagers;
use App\Models\Prisoner;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PrisonerResource extends Resource {
    protected static ?string $model = Prisoner::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $navigationGroup = 'Prisoner Database';
    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form {
        return $form
            ->schema([
                Forms\Components\Section::make('Identity')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('first_name')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('middle_name')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('last_name')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('aka')
                            ->label('AKA')
                            ->maxLength(255),
                        Forms\Components\Select::make('gender')
                            ->options([
                                'male'   => 'Male',
                                'female' => 'Female',
                                'other'  => 'Other',
                            ]),
                        Forms\Components\TextInput::make('race')
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('birthdate'),
                        Forms\Components\DatePicker::make('death_date'),
                        Forms\Components\TextInput::make('age')
                            ->numeric(),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Photo & Description')
                    ->schema([
                        Forms\Components\FileUpload::make('photo')
                            ->image()
                            ->directory('prisoners'),
                        Forms\Components\Textarea::make('description')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Political Information')
                    ->schema([
                        Forms\Components\TagsInput::make('ideologies'),
                        Forms\Components\TagsInput::make('affiliation'),
                        Forms\Components\TextInput::make('era')
                            ->maxLength(255),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Toggle::make('in_custody'),
                        Forms\Components\Toggle::make('released'),
                        Forms\Components\Toggle::make('in_exile'),
                        Forms\Components\Toggle::make('currently_in_exile'),
                        Forms\Components\Toggle::make('imprisoned_or_exiled'),
                        Forms\Components\Toggle::make('awaiting_trial'),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Incarceration Details')
                    ->schema([
                        Forms\Components\TextInput::make('years_in_prison')
                            ->numeric(),
                        Forms\Components\TextInput::make('inmate_number')
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Location')
                    ->schema([
                        Forms\Components\TextInput::make('state')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('address'),
                        Forms\Components\TextInput::make('lat')
                            ->label('Latitude')
                            ->numeric(),
                        Forms\Components\TextInput::make('lng')
                            ->label('Longitude')
                            ->numeric(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Social & Web')
                    ->schema([
                        Forms\Components\TextInput::make('website')
                            ->url()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('twitter')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('facebook')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('instagram')
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Forms\Components\TextInput::make('sort_order')
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('photo')
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('era')
                    ->sortable(),
                Tables\Columns\IconColumn::make('in_custody')
                    ->boolean(),
                Tables\Columns\IconColumn::make('released')
                    ->boolean(),
                Tables\Columns\TextColumn::make('state')
                    ->sortable(),
                Tables\Columns\TextColumn::make('years_in_prison')
                    ->sortable()
                    ->label('Years'),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                Tables\Filters\TernaryFilter::make('in_custody'),
                Tables\Filters\TernaryFilter::make('released'),
                Tables\Filters\TernaryFilter::make('in_exile'),
                Tables\Filters\TernaryFilter::make('awaiting_trial'),
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

    public static function getRelations(): array {
        return [
            RelationManagers\CasesRelationManager::class,
        ];
    }

    public static function getPages(): array {
        return [
            'index'  => Pages\ListPrisoners::route('/'),
            'create' => Pages\CreatePrisoner::route('/create'),
            'edit'   => Pages\EditPrisoner::route('/{record}/edit'),
        ];
    }
}
