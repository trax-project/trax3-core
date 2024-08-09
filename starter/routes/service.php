<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Trax\Framework\Service\Config;
use Trax\Framework\Service\MicroServiceController;
use Trax\Framework\Front\AuthenticatedSessionController;
use Trax\Starter\HandleInertiaRequests;

// Micro-services.

Route::get(
    '/trax/api/starter/check',
    [MicroServiceController::class, 'check']
);

// Welcome pages.

Route::get('/', function () {
    return redirect()->route('statements');
});
Route::get('/home', function () {
    return redirect()->route('statements');
});


Route::middleware([
    'web',
    'auth',
    'trax.context.set',
    HandleInertiaRequests::class,
    AddLinkHeadersForPreloadedAssets::class,
])->group(function () {

    Route::get('/trax/starter/statements', function () {
        return Inertia::render('Data/Statements');
    })->name('statements');

    Route::get('/trax/starter/access', function () {
        return Inertia::render('Access/Access', Config::accessSettings());
    })->name('access');

    Route::get('/trax/starter/settings', function () {
        return Inertia::render('Settings/Settings', Config::appSettings());
    })->name('settings');

    Route::get('/trax/starter/auth/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

Route::middleware([
    'web',
    'guest',
    HandleInertiaRequests::class,
    AddLinkHeadersForPreloadedAssets::class,
])->group(function () {

    Route::get('/trax/starter/auth/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/trax/api/auth/login', [AuthenticatedSessionController::class, 'store'])->name('login-api');
});
