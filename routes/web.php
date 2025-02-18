<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuizController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $quiz = new QuizController();
    $quiz->resultCard('1739699857new-quiz');
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'dashboard'])->middleware('auth')->name('dashboard');
Route::get('/statistics', [DashboardController::class, 'statistics'])->middleware('auth')->name('statistics');
Route::get('/quizzes', [QuizController::class, 'index'])->middleware('auth')->name('quizzes');
Route::get('/quizzes/{quiz}', [QuizController::class, 'edit'])->middleware('auth')->name('quiz-edit');
Route::get('/create-quiz', [QuizController::class, 'create'])->middleware('auth')->name('create-quiz');
Route::get('/quizzes/{quiz}/delete', [QuizController::class, 'destroy'])->middleware('auth')->name('delete-quiz');
Route::get('/show-quiz/{slug}', [QuizController::class, 'show'])->middleware('auth')->name('show-quiz');

Route::post('/start-quiz/{slug}', [QuizController::class, 'startQuiz'])->middleware('auth')->name('start-quiz');
Route::post('/take-quiz/{slug}', [QuizController::class, 'takeQuiz'])->middleware('auth')->name('take-quiz');
Route::post('/create-quiz', [QuizController::class, 'store'])->middleware('auth')->name('store-quiz');
Route::post('/quizzes/{quiz}/update', [QuizController::class, 'update'])->middleware('auth')->name('quiz-update');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
