<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\QuestionController;

Route::get('/', [QuizController::class, 'index'])->name('home');

// Admin / Creation routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('quizzes', QuizController::class)->except(['show', 'index']);
    Route::post('quizzes/{quiz}/questions', [QuestionController::class, 'store'])->name('questions.store');
});

// User attempt routes
Route::get('quizzes/{quiz}', [QuizController::class, 'show'])->name('quizzes.show');
Route::post('quizzes/{quiz}/attempt', [QuizController::class, 'attempt'])->name('quizzes.attempt');
Route::get('quizzes/{quiz}/results/{attempt}', [QuizController::class, 'results'])->name('quizzes.results');
