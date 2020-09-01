<?php

use Avant\LaravelZohoClient\Http\Controllers\OAuthController;
use Avant\LaravelZohoClient\ZohoClientServiceProvider;
use Illuminate\Support\Facades\Route;

Route::middleware(config('zoho-client.middleware'))
    ->prefix(ZohoClientServiceProvider::TAG)
    ->as(ZohoClientServiceProvider::TAG . '.')
    ->group(function () {
        Route::get('authenticate', [OAuthController::class, 'authenticate'])->name('authenticate');
        Route::get('callback', [OAuthController::class, 'callback'])->name('callback');
    });
