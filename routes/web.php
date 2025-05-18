<?php

use Avant\Zoho\Http\Controllers\OAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['web'])
    ->prefix('zoho')
    ->as('zoho.')
    ->group(function () {
        if (config('services.zoho.web_authentication')) {
            Route::get('authenticate', [OAuthController::class, 'authenticate'])->name('authenticate');
            Route::get('callback', [OAuthController::class, 'callback'])->name('callback');
        } else {
            Route::get('callback', fn (Request $request) => $request->getRequestUri())->name('callback');
        }
    });
