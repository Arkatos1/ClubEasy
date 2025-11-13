<?php

use App\Http\Controllers\SimpleTournamentController;
use App\Http\Controllers\TournamentController;
use App\Http\Controllers\TreeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\DashboardController;

// Tournaments Routes
Route::get('/tournaments', [TournamentController::class, 'index'])->name('tournaments.index');
Route::get('/tournaments/{id}', [TournamentController::class, 'show'])->name('tournaments.show');
Route::get('/tournaments/{tournament}/championships/{championship}', [TournamentController::class, 'showChampionship'])->name('tournaments.championship');
Route::post('/tournaments/create-demo', [TournamentController::class, 'createDemo'])->name('tournaments.create-demo');

// Tournament Tree Routes (required by package views)
Route::put('/championships/{championship}/trees', [TournamentController::class, 'updateTree'])->name('tree.update');

// Package Tree Routes (the actual package demo routes)
Route::get('/trees', [TreeController::class, 'index'])->name('tree.index');
Route::get('/trees/{championship}', [TreeController::class, 'show'])->name('tree.show');
Route::post('/trees/{championship}', action: [TreeController::class, 'store'])->name('tree.store');
Route::put('/trees/{championship}', [TreeController::class, 'update'])->name('tree.update');

// Simple tournament routes (using only tournament table)
Route::get('/simple-tournament', [SimpleTournamentController::class, 'index'])->name('simple.tournament');
Route::post('/simple-tournament/generate', [SimpleTournamentController::class, 'generate'])->name('simple.tournament.generate');
Route::get('/simple-tournament/{id}', [SimpleTournamentController::class, 'show'])->name('simple.tournament.show');

// Public Routes - Home page IS the blog
Route::get('/', [HomeController::class, 'index'])->name('home');

// Individual blog post routes
Route::get('/blog/{slug}', [HomeController::class, 'showPost'])->name('blog.show');
Route::get('/topic/{slug}', [HomeController::class, 'topic'])->name('blog.topic');

// Feature-Flagged Routes


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
