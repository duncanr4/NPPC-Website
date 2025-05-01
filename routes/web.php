<?php

use App\Http\Controllers\DonateController;
use App\Http\Controllers\FormSubmissionController;
use App\Http\Controllers\SiteController;
use App\Views\ViewSupport;
use Illuminate\Support\Facades\Route;

Route::get('dev', fn () => ViewSupport::getMenuItems());

Route::controller(DonateController::class)
    ->group(function () {
        Route::get('/donate-callback', 'callback');
    });

Route::controller(SiteController::class)
    ->group(function () {
        Route::get('/', 'home')->name('home');
        Route::get('/site', 'site');
        Route::get('/settings', 'settings');
        Route::get('/news/{slug}', 'article');
        Route::get('history', 'timeline');
        Route::get('annual-report', 'annualReport');
        Route::get('map', 'map');
        Route::get('faq', 'faq');
        Route::get('staff', 'staff');
        Route::get('volunteer', 'volunteer');
        Route::get('board-of-directors', 'boardOfDirectors');
        Route::get('/{slug}', 'page');
    });

Route::controller(FormSubmissionController::class)
    ->group(function () {
        Route::post('/form/{form}', 'submit');
    });
