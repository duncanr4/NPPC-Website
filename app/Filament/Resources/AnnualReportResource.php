<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AnnualReportResource\Pages;
use App\Models\AnnualReport;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class AnnualReportResource extends Resource {
    protected static ?string $model = AnnualReport::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationGroup = 'Content';
    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Form $form): Form {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\FileUpload::make('file')
                    ->label('PDF File')
                    ->disk('public')
                    ->directory('annual-reports')
                    ->maxSize(51200),
                Forms\Components\FileUpload::make('image')
                    ->label('Cover Image')
                    ->helperText('Upload a cover image, or leave blank and click "Generate from PDF" after saving.')
                    ->image()
                    ->disk('public')
                    ->directory('annual-reports/images'),
            ]);
    }

    public static function table(Table $table): Table {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->disk('public'),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([])
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
            'index'  => Pages\ListAnnualReports::route('/'),
            'create' => Pages\CreateAnnualReport::route('/create'),
            'edit'   => Pages\EditAnnualReport::route('/{record}/edit'),
        ];
    }
}
