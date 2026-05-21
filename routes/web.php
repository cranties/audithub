<?php

use App\Http\Controllers\Admin\SubmissionController;
use App\Http\Controllers\Admin\SurveyController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicSurveyController;
use Illuminate\Support\Facades\Route;

// ── Landing / dashboard ────────────────────────────────────────────────────
Route::get('/', HomeController::class)->name('home');
Route::redirect('/dashboard', '/admin/surveys')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// ── Public survey (no auth, rate-limited submit) ───────────────────────────
Route::get('/s/{survey:public_token}', [PublicSurveyController::class, 'show'])
    ->name('survey.show');

Route::get('/s/{survey:public_token}/thank-you', [PublicSurveyController::class, 'thankYou'])
    ->name('survey.thankyou');

Route::middleware('throttle:5,1')->group(function () {
    Route::post('/s/{survey:public_token}/submit', [PublicSurveyController::class, 'store'])
        ->name('survey.store');
});

// ── Admin: protected behind auth ──────────────────────────────────────────
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {

    // Surveys CRUD
    Route::resource('surveys', SurveyController::class)
        ->except(['show'])->names('surveys');

    Route::post('surveys/{survey}/duplicate', [SurveyController::class, 'duplicate'])
        ->name('surveys.duplicate');

    // Submissions (nested under survey for ownership clarity)
    Route::get('surveys/{survey}/submissions',
        [SubmissionController::class, 'index'])->name('surveys.submissions.index');

    Route::get('surveys/{survey}/submissions/{submission}',
        [SubmissionController::class, 'show'])->name('surveys.submissions.show');

    Route::get('surveys/{survey}/submissions/{submission}/pdf',
        [SubmissionController::class, 'downloadPdf'])->name('surveys.submissions.pdf');

    // Attachment download (private storage — auth required)
    Route::get('files/{file}/download',
        [SubmissionController::class, 'downloadFile'])->name('files.download');

    // User management
    Route::get('users', \App\Livewire\Admin\UserManagement::class)->name('users.index');
});

// ── Profile (Breeze) — kept outside admin prefix so route names stay as
//   profile.edit / profile.update / profile.destroy as expected by navigation.blade.php
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

