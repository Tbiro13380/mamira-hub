<?php

use App\Http\Controllers\LetterController;
use App\Http\Controllers\BadgeController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\MemeController;
use App\Http\Controllers\QuizController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    // Letters
    Route::prefix('cartas')->name('letters.')->group(function () {
        Route::get('/', [LetterController::class, 'index'])->name('index');
        Route::get('escrever', [LetterController::class, 'create'])->name('create');
        Route::post('/', [LetterController::class, 'store'])->name('store');
        Route::delete('{letter}', [LetterController::class, 'destroy'])->name('destroy');
        Route::post('{letter}/like', [LetterController::class, 'toggleLike'])->name('like');
        Route::post('{letter}/comments', [LetterController::class, 'storeComment'])->name('comments.store');
    });

    // Badges
    Route::get('conquistas', [BadgeController::class, 'index'])->name('badges.index');
    Route::patch('conquistas/selecionar', [BadgeController::class, 'updateSelected'])->name('badges.update-selected');

    // Leaderboard
    Route::get('leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard.index');

    // Activities (Feed)
    Route::get('atividades', [ActivityController::class, 'index'])->name('activities.index');

    // Photos
    Route::prefix('fotos')->name('photos.')->group(function () {
        Route::get('/', [PhotoController::class, 'index'])->name('index');
        Route::post('/', [PhotoController::class, 'store'])->name('store');
        Route::delete('{photo}', [PhotoController::class, 'destroy'])->name('destroy');
    });

    // Stats
    Route::get('estatisticas', [StatsController::class, 'index'])->name('stats.index');
    Route::post('estatisticas/lagrimas', [StatsController::class, 'addTear'])->name('stats.add-tear');

    // Memes
    Route::prefix('memes')->name('memes.')->group(function () {
        Route::get('/', [MemeController::class, 'index'])->name('index');
        Route::post('/', [MemeController::class, 'store'])->name('store');
        Route::post('{meme}/vote', [MemeController::class, 'vote'])->name('vote');
    });

    // Quizzes
    Route::prefix('quizzes')->name('quizzes.')->group(function () {
        Route::get('/', [QuizController::class, 'index'])->name('index');
        Route::get('criar', [QuizController::class, 'create'])->name('create');
        Route::post('/', [QuizController::class, 'store'])->name('store');
        Route::post('{quiz}/submit', [QuizController::class, 'submit'])->name('submit');
    });
});

/*
|--------------------------------------------------------------------------
| Settings Routes
|--------------------------------------------------------------------------
*/

require __DIR__.'/settings.php';
