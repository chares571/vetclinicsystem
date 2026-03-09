<?php

namespace App\Http\Controllers;

use App\Http\Requests\ForcePasswordUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ForcePasswordController extends Controller
{
    public function edit(): View|RedirectResponse
    {
        $user = auth()->user();

        if (! $user?->must_change_password) {
            if ($user?->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }

            if ($user?->isVeterinaryStaff()) {
                return redirect()->route('staff.dashboard');
            }

            return redirect()->route('client.dashboard');
        }

        return view('auth.force-password-change');
    }

    public function update(ForcePasswordUpdateRequest $request): RedirectResponse
    {
        $request->user()->update([
            'password' => Hash::make($request->validated('password')),
            'must_change_password' => false,
        ]);

        if ($request->user()->isAdmin()) {
            return redirect()->route('admin.dashboard')
                ->with('success', 'Password changed successfully.');
        }

        if ($request->user()->isVeterinaryStaff()) {
            return redirect()->route('staff.dashboard')
                ->with('success', 'Password changed successfully.');
        }

        return redirect()->route('client.dashboard')
            ->with('success', 'Password changed successfully.');
    }
}
