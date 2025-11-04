<?php

use Illuminate\Support\Facades\Route;
use Modules\Membership\Http\Controllers\MembershipController;

Route::middleware(['auth', 'verified'])->prefix('membership')->name('membership.')->group(function () {
    Route::get('/', function () { return view('membership::index'); })->name('index');
    Route::get('/plans', function () { return view('membership::plans'); })->name('plans');
    Route::get('/status', function () { return view('membership::status'); })->name('status');
    Route::post('/subscribe', function () { return redirect()->route('membership.status')->with('success', 'Updated!'); })->name('subscribe');
});
