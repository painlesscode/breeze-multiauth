<?php

namespace App\Http\Controllers\{{Name}}\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function __invoke(Request $request)
    {
        return $request->user('{{name}}')->hasVerifiedEmail()
                    ? redirect()->intended(route('{{name}}.dashboard'))
                    : view('{{name}}.auth.verify-email');
    }
}
