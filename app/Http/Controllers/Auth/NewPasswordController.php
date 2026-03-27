<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request): View
    {
        $verifiedEmail = $request->session()->get('password_reset.email');
        $expiresAt = $request->session()->get('password_reset_expires_at');

        if (! $verifiedEmail || ($expiresAt && now()->greaterThan($expiresAt))) {
            $request->session()->forget(['password_reset.email', 'password_reset_verified_at', 'password_reset_expires_at']);

            return redirect()->route('password.request')
                ->withErrors(['email' => __('Please verify your email with the code we sent before resetting your password.')]);
        }

        return view('auth.reset-password', ['email' => $verifiedEmail]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $verifiedEmail = $request->session()->get('password_reset.email');
        $expiresAt = $request->session()->get('password_reset_expires_at');
        $requestedEmail = strtolower($request->string('email')->value());

        if ($expiresAt && now()->greaterThan($expiresAt)) {
            $request->session()->forget(['password_reset.email', 'password_reset_verified_at', 'password_reset_expires_at']);

            return redirect()->route('password.request')
                ->withErrors(['email' => __('Your verification code expired. Please request a new one.')]);
        }

        if (! $verifiedEmail || $verifiedEmail !== $requestedEmail) {
            return redirect()->route('password.request')
                ->withErrors(['email' => __('Please complete email verification before resetting your password.')]);
        }

        $user = User::where('email', $verifiedEmail)->first();

        if (! $user) {
            $request->session()->forget(['password_reset.email', 'password_reset_verified_at', 'password_reset_expires_at']);

            return redirect()->route('password.request')
                ->withErrors(['email' => __('We could not find that account. Please request a new code.')]);
        }

        $user->forceFill([
            'password' => Hash::make($request->password),
            'remember_token' => Str::random(60),
        ])->save();

        event(new PasswordReset($user));

        $request->session()->forget(['password_reset.email', 'password_reset_verified_at', 'password_reset_expires_at']);

        return redirect()->route('login')->with('status', __('Your password has been reset. You can now log in.'));
    }
}
