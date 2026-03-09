<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MedicalRecordStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->isStaffOrAdmin();
    }

    public function rules(): array
    {
        return [
            'pet_id' => ['required', 'integer', 'exists:pets,id'],
            'complaint' => ['required', 'string', 'max:1000'],
            'diagnosis' => ['nullable', 'string', 'max:1000'],
            'treatment' => ['nullable', 'string', 'max:1000'],
            'visit_date' => ['required', 'date'],
        ];
    }
}
