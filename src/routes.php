<?php

use Illuminate\Support\Facades\Route;
use Guysolamour\Hootsuite\Http\Controllers\HootsuiteController;

Route::get('laravel-hootsuite/tokens', [HootsuiteController::class, 'saveTokens'])->name('hootsuite.redirect.uri');
