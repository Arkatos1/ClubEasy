<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlayerController;

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

Route::get('/players', function () {
    return view('pages.players');
});

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
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::group(['middleware' => ['role:admin']], function () {
    Route::get('/admin/users', 'UserManagementController@index');
});

require __DIR__.'/auth.php';
