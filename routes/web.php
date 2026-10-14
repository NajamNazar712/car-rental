<?php

use App\Http\Controllers\PageController;
use App\Http\Middleware\SetLocale;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect('/'.config('locales.default')));

Route::prefix('{locale}')
    ->where(['locale' => implode('|', array_keys(config('locales.supported')))])
    ->middleware(SetLocale::class)
    ->group(function (): void {
        Route::get('/', [PageController::class, 'home'])->name('home');
    });
