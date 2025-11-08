<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\DashboardController;

// Public Routes
Route::get('/', function () {
    return view('home');
});

// Feature-Flagged Routes
Route::get('/tournaments', function () {
    return view('tournaments');
});

// Resource Routes
Route::resource('players', PlayerController::class);

// Navigation Pages
Route::get('/matches', function () {
    return view('pages.matches');
});

Route::get('/results', function () {
    return view('pages.results');
});

Route::get('/about', function () {
    return view('pages.about');
});

// Auth Protected Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Membership Routes
Route::middleware(['auth', 'verified'])->prefix('membership')->name('membership.')->group(function () {
    Route::get('/', function () { return view('membership::index'); })->name('index');
    Route::get('/plans', function () { return view('membership::plans'); })->name('plans');
    Route::get('/status', function () { return view('membership::status'); })->name('status');
    Route::post('/subscribe', function () { return redirect()->route('membership.status')->with('success', 'Updated!'); })->name('subscribe');
});

// Trainer Routes
Route::middleware(['auth', 'verified', 'role:trainer|administrator'])->prefix('trainer')->name('trainer.')->group(function () {
    Route::get('/dashboard', function () {
        return view('trainer.dashboard');
    })->name('dashboard');
});

// System Admin Routes (Custom admin to avoid conflict with Twill)
Route::middleware(['auth', 'verified', 'role:administrator'])->prefix('system')->name('system.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    Route::get('/users', function () {
        return view('admin.users');
    })->name('users');

    Route::get('/roles', function () {
        return view('admin.roles');
    })->name('roles');
});

Route::get('/users', function () {
    return app()->make(\jeremykenedy\LaravelUsers\App\Http\Controllers\UsersManagementController::class)->index();
})->name('users')->middleware(['auth', 'verified', 'role:administrator']);

Route::middleware(['auth', 'verified'])->prefix('membership')->name('membership.')->group(function () {
    Route::get('/', function () { return view('membership::index'); })->name('index');
    Route::get('/plans', function () { return view('membership::plans'); })->name('plans');
    Route::get('/status', function () { return view('membership::status'); })->name('status');
    Route::post('/subscribe', function () { return redirect()->route('membership.status')->with('success', 'Updated!'); })->name('subscribe');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Only allow administrators to access user management
    Route::middleware(['role:administrator'])->group(function () {
        Route::resource('users', \jeremykenedy\LaravelUsers\App\Http\Controllers\UsersManagementController::class);
    });
});


require __DIR__.'/auth.php';
