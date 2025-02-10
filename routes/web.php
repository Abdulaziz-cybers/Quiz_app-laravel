<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/create-quiz', function () {
    return view('dashboard.create');
})->middleware(['auth', 'verified'])->name('create-quiz');
Route::get('/quizzes', function () {
    return view('dashboard.quizzes');
})->middleware(['auth', 'verified'])->name('quizzes');
Route::get('/statistics', function () {
    return view('dashboard.statistics');
})->middleware(['auth', 'verified'])->name('statistics');
Route::get('/take-quiz', function () {
    return view('quiz.take-quiz');
})->middleware(['auth', 'verified'])->name('take-quiz');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
