<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Services\ActivityLogService;
use App\Services\AuditLogService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function __construct(
        private readonly ActivityLogService $activityLogService,
        private readonly AuditLogService $auditLogService
    ) {
    }

    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(RegisterRequest $request): RedirectResponse
    {
        $user = User::create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'password' => Hash::make($request->validated('password')),
            'role' => User::ROLE_CLIENT,
            'is_active' => true,
            'must_change_password' => false,
        ]);

        event(new Registered($user));
        $this->activityLogService->log($user, 'New Client Registered', sprintf('%s registered a new client account.', $user->name));
        $this->auditLogService->log(
            $user,
            'created',
            'user',
            $user->id,
            sprintf('%s self-registered a client account.', $user->name),
            ['role' => $user->role]
        );

        return redirect()
            ->route('login')
            ->with('status', 'Registration successful. Please log in to continue.');
    }
}
