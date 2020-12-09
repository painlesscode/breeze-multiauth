<?php

namespace Painless\BreezeMultiAuth;

class MultiAuthRouteMethods
{

    public static function multiauth(): callable
    {
        return function ($namespace, $guard, $options = []) {

            $this->middleware(['auth:'.$guard])->name($guard.'.')->prefix($guard)->group(function() use ($options, $namespace) {

                $this->get('/dashboard', ["App\Http\Controllers\\$namespace\DashboardController", 'index'])->name('dashboard');

                if ($options['verify'] ?? true) {
                    $this->get('/verify-email', ["App\Http\Controllers\\$namespace\Auth\EmailVerificationPromptController", '__invoke'])
                        ->name('verification.notice');

                    $this->get('/verify-email/{id}/{hash}', ["App\Http\Controllers\\$namespace\Auth\VerifyEmailController", '__invoke'])
                        ->middleware(['signed', 'throttle:6,1'])
                        ->name('verification.verify');

                    $this->post('/email/verification-notification', ["App\Http\Controllers\\$namespace\Auth\EmailVerificationNotificationController", 'store'])
                        ->middleware(['throttle:6,1'])
                        ->name('verification.send');
                }

                if ($options['confirm'] ?? true){
                    $this->get('/confirm-password', ["App\Http\Controllers\\$namespace\Auth\ConfirmablePasswordController", 'show'])
                        ->name('password.confirm');

                    $this->post('/confirm-password', ["App\Http\Controllers\\$namespace\Auth\ConfirmablePasswordController", 'store']);
                }

                $this->post('/logout', ["App\Http\Controllers\\$namespace\Auth\AuthenticatedSessionController", 'destroy'])
                    ->name('logout');
            });

            $this->middleware(['guest:'.$guard])->name($guard.'.')->prefix($guard)->group(function() use ($options, $namespace) {

                $this->get('/login', ["App\Http\Controllers\\$namespace\Auth\AuthenticatedSessionController", 'create'])
                    ->name('login');

                $this->post('/login', ["App\Http\Controllers\\$namespace\Auth\AuthenticatedSessionController", 'store']);

                if ($options['register'] ?? true) {
                    $this->get('/register', ["App\Http\Controllers\\$namespace\Auth\RegisteredUserController", 'create'])
                        ->name('register');

                    $this->post('/register', ["App\Http\Controllers\\$namespace\Auth\RegisteredUserController", 'store']);
                }

                if ($options['reset'] ?? true) {
                    $this->get('/forgot-password', ["App\Http\Controllers\\$namespace\Auth\PasswordResetLinkController", 'create'])
                        ->name('password.request');

                    $this->post('/forgot-password', ["App\Http\Controllers\\$namespace\Auth\PasswordResetLinkController", 'store'])
                        ->name('password.email');

                    $this->get('/reset-password/{token}', ["App\Http\Controllers\\$namespace\Auth\NewPasswordController", 'create'])
                        ->name('password.reset');

                    $this->post('/reset-password', ["App\Http\Controllers\\$namespace\Auth\NewPasswordController", 'store'])
                        ->name('password.update');
                }
            });
        };
    }
}
