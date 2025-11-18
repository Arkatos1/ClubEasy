<?php

use App\Http\Controllers\TreeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CalendarController;
use Modules\Membership\Http\Controllers\MembershipController;

// Public Routes - Home page IS the blog
Route::get('/', [HomeController::class, 'index'])->name('home');

// Individual blog post routes
Route::get('/blog/{slug}', [HomeController::class, 'showPost'])->name('blog.show');
Route::get('/topic/{slug}', [HomeController::class, 'topic'])->name('blog.topic');

// Feature-Flagged Routes
// Tournaments routes
Route::get('/tournaments', [App\Http\Controllers\TreeController::class, 'index'])->name('tournaments.index');
Route::post('/tournaments', [App\Http\Controllers\TreeController::class, 'store'])->name('tournaments.store');
Route::put('/tournaments/{championship}', [App\Http\Controllers\TreeController::class, 'update'])->name('tournaments.update');

// Resource Routes
Route::resource('players', PlayerController::class);

// Navigation Pages
Route::get('/sports', function () {
    return view('pages.sports');
});

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

// Calendar API Route (added to web.php since you don't have api.php)
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

Route::middleware(['auth', 'verified'])->prefix('membership')->name('membership.')->group(function () {
    Route::get('/', [MembershipController::class, 'index'])->name('index');
    Route::get('/plans', [MembershipController::class, 'plans'])->name('plans');
    Route::get('/status', [MembershipController::class, 'status'])->name('status');
    Route::post('/subscribe', [MembershipController::class, 'subscribe'])->name('subscribe');
    Route::post('/join', [MembershipController::class, 'join'])->name('join');
    Route::delete('/leave', [MembershipController::class, 'leave'])->name('leave');
    Route::get('/confirm-payment', [MembershipController::class, 'confirmPayment'])->name('confirm-payment');
    Route::post('/confirm-payment', [MembershipController::class, 'processConfirmation'])->name('process-confirmation');
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

Route::middleware(['auth', 'verified'])->group(function () {
    // Only allow administrators to access user management
    Route::middleware(['role:administrator'])->group(function () {
        Route::resource('users', \jeremykenedy\LaravelUsers\App\Http\Controllers\UsersManagementController::class);
    });
});

require __DIR__.'/auth.php';

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
