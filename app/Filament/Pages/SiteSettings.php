<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class SiteSettings extends Page implements HasForms {
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Site Settings';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 100;
    protected static string $view = 'filament.pages.site-settings';

    public ?array $data = [];

    public function mount(): void {
        $this->form->fill([
            'twitter_url'   => SiteSetting::get('twitter_url', ''),
            'facebook_url'  => SiteSetting::get('facebook_url', ''),
            'instagram_url' => SiteSetting::get('instagram_url', ''),
            'youtube_url'   => SiteSetting::get('youtube_url', ''),
            'tiktok_url'    => SiteSetting::get('tiktok_url', ''),
        ]);
    }

    public function form(Form $form): Form {
        return $form
            ->schema([
                Forms\Components\Section::make('Social Media Links')
                    ->description('Update the social media URLs displayed in the site footer.')
                    ->schema([
                        Forms\Components\TextInput::make('twitter_url')
                            ->label('Twitter / X')
                            ->url()
                            ->placeholder('https://twitter.com/yourhandle')
                            ->prefixIcon('heroicon-o-link'),
                        Forms\Components\TextInput::make('facebook_url')
                            ->label('Facebook')
                            ->url()
                            ->placeholder('https://www.facebook.com/yourpage')
                            ->prefixIcon('heroicon-o-link'),
                        Forms\Components\TextInput::make('instagram_url')
                            ->label('Instagram')
                            ->url()
                            ->placeholder('https://www.instagram.com/yourhandle')
                            ->prefixIcon('heroicon-o-link'),
                        Forms\Components\TextInput::make('youtube_url')
                            ->label('YouTube')
                            ->url()
                            ->placeholder('https://www.youtube.com/yourchannel')
                            ->prefixIcon('heroicon-o-link'),
                        Forms\Components\TextInput::make('tiktok_url')
                            ->label('TikTok')
                            ->url()
                            ->placeholder('https://www.tiktok.com/@yourhandle')
                            ->prefixIcon('heroicon-o-link'),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            SiteSetting::set($key, $value);
        }

        Notification::make()
            ->title('Settings saved')
            ->success()
            ->send();
    }

    protected function getFormActions(): array {
        return [
            Action::make('save')
                ->label('Save Settings')
                ->submit('save'),
        ];
    }
}
