<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuizController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
Route::get('/statistics', [DashboardController::class, 'statistics'])->name('statistics');
Route::get('/quizzes', [QuizController::class, 'index'])->name('quizzes');
Route::get('/quizzes/{quiz}', [QuizController::class, 'edit'])->name('quiz-edit');
Route::get('/create-quiz', [QuizController::class, 'create'])->name('create-quiz');
Route::get('/quizzes/{quiz}/delete', [QuizController::class, 'destroy'])->name('delete-quiz');
Route::get('/take-quiz/{slug}', [QuizController::class, 'startQuiz'])->name('start-quiz');

Route::post('/take-quiz/{slug}', [QuizController::class, 'takeQuiz'])->name('take-quiz');
Route::post('/create-quiz', [QuizController::class, 'store'])->name('store-quiz');
Route::post('/quizzes/{quiz}/update', [QuizController::class, 'update'])->name('quiz-update');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
