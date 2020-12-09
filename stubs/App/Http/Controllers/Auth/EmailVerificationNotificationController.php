<?php

namespace App\Http\Controllers\{{Name}}\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->user('{{name}}')->hasVerifiedEmail()) {
            return redirect()->intended(route('{{name}}.dashboard'));
        }

        $request->user('{{name}}')->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }
}
