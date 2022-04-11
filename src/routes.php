<?php

use Illuminate\Support\Facades\Route;
use Guysolamour\Hootsuite\Http\Controllers\HootsuiteController;


Route::prefix('laravel-hootsuite')->group(function () {
    Route::get('', [HootsuiteController::class, 'redirectUri'])->name('hootsuite.redirect.uri');
    Route::get('/tokens', [HootsuiteController::class, 'saveTokens'])->name('hootsuite.redirect.uri.tokens');
    Route::post('/refresh/token', [HootsuiteController::class, 'refreshToken'])->name('hootsuite.refresh.token');
});

