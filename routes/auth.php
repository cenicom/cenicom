<?php

declare(strict_types=1);

use App\Core\Http\Controllers\AuthenticationController;
use App\Core\Http\Controllers\PasswordRecoveryController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticationController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [AuthenticationController::class, 'login'])
        ->name('login.authenticate');

    Route::get('/forgot-password', [
        PasswordRecoveryController::class,
        'showRequestForm',
    ])->name('password.request');

    Route::post('/forgot-password', [
        PasswordRecoveryController::class,
        'sendResetLink',
    ])
        ->middleware('throttle:6,1')
        ->name('password.email');

    Route::get('/reset-password/{token}', [
        PasswordRecoveryController::class,
        'showResetForm',
    ])->name('password.reset');

    Route::post('/reset-password', [
        PasswordRecoveryController::class,
        'reset',
    ])
        ->middleware('throttle:6,1')
        ->name('password.update');
});

Route::post('/logout', [AuthenticationController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function (): void {
    Route::get('/email/verify', function (): View|RedirectResponse {
        if (request()->user()->hasVerifiedEmail()) {
            return redirect('/');
        }

        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (
        EmailVerificationRequest $request,
    ): RedirectResponse {
        $request->fulfill();

        return redirect('/');
    })
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('/email/verification-notification', function (
        Request $request,
    ): RedirectResponse {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect('/');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    })
        ->middleware('throttle:6,1')
        ->name('verification.send');
});