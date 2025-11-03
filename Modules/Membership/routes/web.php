<?php

use Illuminate\Support\Facades\Route;
use Modules\Membership\Http\Controllers\MembershipController;

Route::middleware(['auth', 'verified'])->prefix('membership')->name('membership.')->group(function () {
    Route::get('/', [MembershipController::class, 'index'])->name('index');
    Route::get('/plans', [MembershipController::class, 'plans'])->name('plans');
    Route::get('/status', [MembershipController::class, 'status'])->name('status');
    Route::post('/subscribe', [MembershipController::class, 'subscribe'])->name('subscribe');
});
