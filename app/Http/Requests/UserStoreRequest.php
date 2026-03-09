<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->isAdmin();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique(User::class, 'email')],
            'role' => ['required', Rule::in([User::ROLE_ADMIN, User::ROLE_VETERINARY_STAFF, User::ROLE_CLIENT])],
            'is_active' => ['nullable', 'boolean'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'must_change_password' => ['nullable', 'boolean'],
        ];
    }
}
