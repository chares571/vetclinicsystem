<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PetStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    public function rules(): array
    {
        return [
            'contact_number' => ['required', 'string', 'max:50'],
            'pet_name' => ['required', 'string', 'max:255'],
            'species' => ['required', 'string', 'max:120'],
            'breed' => ['nullable', 'string', 'max:120'],
            'sex' => ['required', 'in:male,female'],
            'age_value' => ['required', 'integer', 'min:0', 'max:240'],
            'age_type' => ['required', 'in:month,year'],
            'owner_user_id' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }
}
