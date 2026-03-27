<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetCode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class PasswordResetCodeController extends Controller
{
    public function show(Request $request): View
    {
        $email = $request->query('email') ?? $request->session()->get('password_reset.email');

        return view('auth.verify-reset-code', ['email' => $email]);
    }

    public function verify(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'code' => ['required', 'digits:6'],
        ]);

        $email = strtolower($validated['email']);
        $record = PasswordResetCode::where('email', $email)->first();

        if (! $record) {
            return back()->withErrors(['code' => __('No verification code found for that email. Please request a new one.')]);
        }

        if ($record->attempts >= 5) {
            return back()->withErrors(['code' => __('Too many attempts. Please request a new verification code.')]);
        }

        if ($record->isExpired()) {
            $record->delete();

            return back()->withErrors(['code' => __('This code has expired. Please request a new one.')]);
        }

        if (! Hash::check($validated['code'], $record->code_hash)) {
            $record->increment('attempts');

            return back()->withErrors(['code' => __('The code you entered is incorrect.')]);
        }

        $request->session()->put([
            'password_reset.email' => $email,
            'password_reset_verified_at' => now(),
            'password_reset_expires_at' => $record->expires_at,
        ]);

        $record->delete();

        return redirect()->route('password.reset')
            ->with('status', __('Verification successful. Set your new password below.'));
    }
}
