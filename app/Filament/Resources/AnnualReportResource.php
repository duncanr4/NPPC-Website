<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AnnualReportResource\Pages;
use App\Models\AnnualReport;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

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
                    ->acceptedFileTypes(['application/pdf'])
                    ->maxSize(51200) // 50MB
                    ->afterStateUpdated(function ($state, $set) {
                        // Auto-generate cover image from first page of PDF
                        if ($state && extension_loaded('imagick')) {
                            try {
                                $tempPath = is_string($state) ? storage_path('app/public/'.$state) : $state->getRealPath();
                                $imagick = new \Imagick();
                                $imagick->setResolution(200, 200);
                                $imagick->readImage($tempPath.'[0]');
                                $imagick->setImageFormat('jpg');
                                $imagick->setImageCompressionQuality(85);

                                $filename = 'annual-reports/images/'.pathinfo(is_string($state) ? $state : $state->getClientOriginalName(), PATHINFO_FILENAME).'.jpg';
                                \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $imagick->getImageBlob());
                                $imagick->clear();
                                $imagick->destroy();

                                $set('image', $filename);
                            } catch (\Exception $e) {
                                // Imagick failed — user can upload image manually
                            }
                        }
                    })
                    ->reactive(),
                Forms\Components\FileUpload::make('image')
                    ->label('Cover Image')
                    ->helperText('Auto-generated from PDF if Imagick is installed. Otherwise upload manually.')
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
