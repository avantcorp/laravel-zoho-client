<?php

use Avant\ZohoClient\Http\Controllers\OAuthController;
use Illuminate\Support\Facades\Route;

Route::middleware(config('zoho_client.middleware'))
    ->prefix('zoho-client')
    ->as('zoho-client.')
    ->group(function () {
        Route::get('authenticate', [OAuthController::class, 'authenticate'])->name('authenticate');
        Route::get('callback', [OAuthController::class, 'callback'])->name('callback');
    });
