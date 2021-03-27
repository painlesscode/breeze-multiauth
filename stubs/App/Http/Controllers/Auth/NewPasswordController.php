<?php

namespace App\Http\Controllers\{{Name}}\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     *
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        return view('{{name}}.auth.reset-password', ['request' => $request]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|confirmed|min:8',
        ]);

        // Here we will attempt to reset the {{name}}'s password. If it is successful we
        // will update the password on an actual {{name}} model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = Password::broker('{{names}}')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (${{name}}) use ($request) {
                ${{name}}->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset(${{name}}));
            }
        );

        // If the password was successfully reset, we will redirect the {{name}} back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        return $status == Password::PASSWORD_RESET
                    ? redirect()->route('{{name}}.login')->with('status', __($status))
                    : back()->withInput($request->only('email'))
                            ->withErrors(['email' => __($status)]);
    }
}
