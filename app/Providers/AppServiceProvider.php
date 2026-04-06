<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Disable Livewire's multiple root element detection
        // Our File Explorer page uses style/script tags alongside content
        \Livewire\Features\SupportMultipleRootElementDetection\SupportMultipleRootElementDetection::$disabled = true;
    }
}
