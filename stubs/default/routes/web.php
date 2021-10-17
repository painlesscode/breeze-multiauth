<?php

use App\Http\Controllers\{{Name}}\Auth\AuthenticatedSessionController;
use App\Http\Controllers\{{Name}}\Auth\ConfirmablePasswordController;
use App\Http\Controllers\{{Name}}\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\{{Name}}\Auth\EmailVerificationPromptController;
use App\Http\Controllers\{{Name}}\Auth\NewPasswordController;
use App\Http\Controllers\{{Name}}\Auth\PasswordResetLinkController;
use App\Http\Controllers\{{Name}}\Auth\RegisteredUserController;
use App\Http\Controllers\{{Name}}\Auth\VerifyEmailController;
use App\Http\Controllers\{{Name}}\DashboardController;
use Illuminate\Support\Facades\Route;

Route::prefix('{{name}}')->name('{{name}}.')->group(function () {

    Route::get('/', [DashboardController::class, 'index'])
        ->middleware('auth:{{name}}');

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('auth:{{name}}')
        ->name('dashboard');

    Route::get('/register', [RegisteredUserController::class, 'create'])
        ->middleware('guest:{{name}}')
        ->name('register');

    Route::post('/register', [RegisteredUserController::class, 'store'])
        ->middleware('guest:{{name}}');

    Route::get('/login', [AuthenticatedSessionController::class, 'create'])
        ->middleware('guest:{{name}}')
        ->name('login');

    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('guest:{{name}}');

    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])
        ->middleware('guest:{{name}}')
        ->name('password.request');

    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
        ->middleware('guest:{{name}}')
        ->name('password.email');

    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])
        ->middleware('guest:{{name}}')
        ->name('password.reset');

    Route::post('/reset-password', [NewPasswordController::class, 'store'])
        ->middleware('guest:{{name}}')
        ->name('password.update');

    Route::get('/verify-email', [EmailVerificationPromptController::class, '__invoke'])
        ->middleware('auth:{{name}}')
        ->name('verification.notice');

    Route::get('/verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
        ->middleware(['auth:{{name}}', 'signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware(['auth:{{name}}', 'throttle:6,1'])
        ->name('verification.send');

    Route::get('/confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->middleware('auth:{{name}}')
        ->name('password.confirm');

    Route::post('/confirm-password', [ConfirmablePasswordController::class, 'store'])
        ->middleware('auth:{{name}}');

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->middleware('auth:{{name}}')
        ->name('logout');
});
