<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PetitionResource\Pages;
use App\Filament\Resources\PetitionResource\RelationManagers;
use App\Models\Petition;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PetitionResource extends Resource {
    protected static ?string $model = Petition::class;
    protected static ?string $navigationIcon = 'heroicon-o-megaphone';
    protected static ?string $navigationGroup = 'Content';
    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Form $form): Form {
        return $form
            ->schema([
                Forms\Components\Section::make('Petition Details')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\FileUpload::make('image')
                            ->image()
                            ->disk('public')
                            ->directory('petitions'),
                        \FilamentTiptapEditor\TiptapEditor::make('body')
                            ->label('Description / Call to Action')
                            ->profile('default')
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Section::make('Message Template')
                    ->schema([
                        Forms\Components\TextInput::make('recipients')
                            ->placeholder('e.g. Your U.S. Senators and House Representatives')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('suggested_subject')
                            ->label('Suggested Subject Line')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('suggested_message')
                            ->label('Suggested Message Body')
                            ->rows(6)
                            ->helperText('Pre-filled message that signers can customize'),
                    ]),
                Forms\Components\Section::make('Settings')
                    ->schema([
                        Forms\Components\TextInput::make('signature_goal')
                            ->numeric()
                            ->default(10000),
                        Forms\Components\Toggle::make('published')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('signatures_count')
                    ->counts('signatures')
                    ->label('Signatures'),
                Tables\Columns\TextColumn::make('signature_goal')
                    ->label('Goal'),
                Tables\Columns\IconColumn::make('published')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('M j, Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getRelations(): array {
        return [
            RelationManagers\SignaturesRelationManager::class,
        ];
    }

    public static function getPages(): array {
        return [
            'index'  => Pages\ListPetitions::route('/'),
            'create' => Pages\CreatePetition::route('/create'),
            'edit'   => Pages\EditPetition::route('/{record}/edit'),
        ];
    }
}
