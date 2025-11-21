<?php

use App\Http\Controllers\TreeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\PaymentAdminController;
use App\Http\Controllers\GalleryController;
use Modules\Membership\Http\Controllers\MembershipController;

// Public Routes - Home page
Route::get('/', [HomeController::class, 'index'])->name('home');

// Individual blog post routes
Route::get('/blog/{slug}', [HomeController::class, 'showPost'])->name('blog.show');
Route::get('/topic/{slug}', [HomeController::class, 'topic'])->name('blog.topic');

// Tournaments routes
Route::get('/tournaments', [TreeController::class, 'index'])->name('tournaments.index');
Route::post('/tournaments', [TreeController::class, 'store'])->name('tournaments.store');
Route::put('/tournaments/{championship}', [TreeController::class, 'update'])->name('tournaments.update');
Route::post('/tournaments/{tournament}/delete', [TreeController::class, 'destroyTournament'])->name('tournaments.destroy');
Route::post('/championships/{championship}/delete', [TreeController::class, 'destroyChampionship'])->name('championships.destroy');

// Resource Routes
Route::resource('players', PlayerController::class);

// Navigation Pages
Route::get('/sports', function () {
    return view('pages.sports');
});

Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery');

Route::get('/administration', function () {
    return view('administration');
})->name('administration')->middleware(['auth', 'role:administrator']);

Route::get('/results', function () {
    return view('pages.results');
});

Route::get('/about', function () {
    return view('pages.about');
});

// Calendar Page
Route::get('/calendar', function () {
    return view('calendar');
});

// Calendar API Route
Route::get('/api/calendar-events', [CalendarController::class, 'index']);

// Auth Protected Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/myprofile', [DashboardController::class, 'index'])->name('myprofile');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Membership Routes
Route::middleware(['auth', 'verified'])->prefix('membership')->name('membership.')->group(function () {
    Route::get('/', [MembershipController::class, 'index'])->name('index');
    Route::post('/subscribe', [MembershipController::class, 'subscribe'])->name('subscribe');
    Route::post('/join', [MembershipController::class, 'join'])->name('join');
    Route::delete('/leave', [MembershipController::class, 'leave'])->name('leave');
    Route::post('/confirm-payment', [MembershipController::class, 'processConfirmation'])->name('confirm-payment');
    Route::post('/cancel-payment', [MembershipController::class, 'cancelPayment'])->name('cancel-payment');
});

// Administration Routes
Route::middleware(['auth', 'verified', 'role:administrator'])->prefix('administration')->name('administration.')->group(function () {
    // Payment Management Routes
    Route::get('/payments', [PaymentAdminController::class, 'index'])->name('payments');
    Route::get('/payments/pending', [PaymentAdminController::class, 'pending'])->name('payments.pending');
    Route::post('/payments/{membership}/verify', [PaymentAdminController::class, 'verifyPayment'])->name('payments.verify');
    Route::post('/payments/{membership}/reject', [PaymentAdminController::class, 'rejectPayment'])->name('payments.reject');
});

// Trainer Routes
Route::middleware(['auth', 'verified', 'role:trainer|administrator'])->prefix('trainer')->name('trainer.')->group(function () {
    Route::get('/dashboard', function () {
        return view('trainer.dashboard');
    })->name('dashboard');
});

// System Admin Routes
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

// Laravel Users Management
Route::get('/users', function () {
    return app()->make(\jeremykenedy\LaravelUsers\App\Http\Controllers\UsersManagementController::class)->index();
})->name('users')->middleware(['auth', 'verified', 'role:administrator']);

Route::middleware(['auth', 'verified'])->group(function () {
    Route::middleware(['role:administrator'])->group(function () {
        Route::resource('users', \jeremykenedy\LaravelUsers\App\Http\Controllers\UsersManagementController::class);
    });
});

require __DIR__.'/auth.php';

// Canvas UI Routes
Route::prefix('canvas-ui')->group(function () {
    Route::prefix('api')->group(function () {
        Route::get('posts', [\App\Http\Controllers\CanvasUiController::class, 'getPosts']);
        Route::get('posts/{slug}', [\App\Http\Controllers\CanvasUiController::class, 'showPost'])
             ->middleware('Canvas\Http\Middleware\Session');

        Route::get('tags', [\App\Http\Controllers\CanvasUiController::class, 'getTags']);
        Route::get('tags/{slug}', [\App\Http\Controllers\CanvasUiController::class, 'showTag']);
        Route::get('tags/{slug}/posts', [\App\Http\Controllers\CanvasUiController::class, 'getPostsForTag']);

        Route::get('topics', [\App\Http\Controllers\CanvasUiController::class, 'getTopics']);
        Route::get('topics/{slug}', [\App\Http\Controllers\CanvasUiController::class, 'showTopic']);
        Route::get('topics/{slug}/posts', [\App\Http\Controllers\CanvasUiController::class, 'getPostsForTopic']);

        Route::get('users/{id}', [\App\Http\Controllers\CanvasUiController::class, 'showUser']);
        Route::get('users/{id}/posts', [\App\Http\Controllers\CanvasUiController::class, 'getPostsForUser']);
    });

    Route::get('/{view?}', [\App\Http\Controllers\CanvasUiController::class, 'index'])
         ->where('view', '(.*)')
         ->name('canvas-ui');
});
