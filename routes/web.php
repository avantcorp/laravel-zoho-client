<?php

use Avant\Zoho\Http\Controllers\OAuthController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web'])
    ->prefix('zoho')
    ->as('zoho.')
    ->group(function () {
        Route::get('authenticate', [OAuthController::class, 'authenticate'])->name('authenticate');
        Route::get('callback', [OAuthController::class, 'callback'])->name('callback');
    });
