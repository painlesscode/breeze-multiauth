<?php

namespace App\Http\Controllers\{{Name}}\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use App\Http\Requests\{{Name}}\Auth\EmailVerificationRequest;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated {{name}}'s email address as verified.
     *
     * @param  \Illuminate\Foundation\Auth\EmailVerificationRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function __invoke(EmailVerificationRequest $request)
    {
        if ($request->user('{{name}}')->hasVerifiedEmail()) {
            return redirect()->intended(route('{{name}}.dashboard').'?verified=1');
        }

        if ($request->user('{{name}}')->markEmailAsVerified()) {
            event(new Verified($request->user('{{name}}')));
        }

        return redirect()->intended(route('{{name}}.dashboard').'?verified=1');
    }
}
