<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TopicResource\Pages;
use App\Models\Topic;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TopicResource extends Resource {
    protected static ?string $model = Topic::class;
    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationGroup = 'Content';
    protected static ?string $navigationLabel = 'Topic Explorer';
    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Form $form): Form {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('parent_id')
                    ->label('Parent Topic')
                    ->relationship('parent', 'title')
                    ->nullable()
                    ->searchable()
                    ->preload()
                    ->helperText('Leave empty for a top-level topic'),
                Forms\Components\FileUpload::make('image')
                    ->image()
                    ->disk('public')
                    ->directory('topics')
                    ->helperText('Background image for this topic section'),
                \FilamentTiptapEditor\TiptapEditor::make('body')
                    ->profile('default')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('sort_order')
                    ->numeric()
                    ->default(0),
                Forms\Components\Toggle::make('published')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('parent.title')
                    ->label('Parent'),
                Tables\Columns\TextColumn::make('children_count')
                    ->counts('children')
                    ->label('Sub-topics'),
                Tables\Columns\TextColumn::make('sort_order')
                    ->sortable(),
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
            'index'  => Pages\ListTopics::route('/'),
            'create' => Pages\CreateTopic::route('/create'),
            'edit'   => Pages\EditTopic::route('/{record}/edit'),
        ];
    }
}
