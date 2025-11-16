<?php

use Illuminate\Support\Facades\Route;
use Modules\Membership\Http\Controllers\MembershipController;

// Remove the Route::middleware() wrapper since it's now in RouteServiceProvider
Route::get('/', [MembershipController::class, 'index'])->name('index');
Route::get('/plans', [MembershipController::class, 'plans'])->name('plans');
Route::get('/status', [MembershipController::class, 'status'])->name('status');
Route::post('/subscribe', [MembershipController::class, 'subscribe'])->name('subscribe');
Route::post('/join', [MembershipController::class, 'join'])->name('join');
Route::delete('/leave', [MembershipController::class, 'leave'])->name('leave');
Route::get('/confirm-payment', [MembershipController::class, 'confirmPayment'])->name('confirm-payment');
Route::post('/confirm-payment', [MembershipController::class, 'processConfirmation'])->name('process-confirmation');
