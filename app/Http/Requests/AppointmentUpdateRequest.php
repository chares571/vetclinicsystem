<?php

namespace App\Http\Requests;

class AppointmentUpdateRequest extends AppointmentStoreRequest
{
    public function rules(): array
    {
        $rules = parent::rules();

        $rules['pet_id'] = ['required', 'integer', 'exists:pets,id'];
        unset($rules['pet_name'], $rules['owner_name'], $rules['contact_number']);

        return $rules;
    }
}
