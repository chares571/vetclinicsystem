<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Pet;
use App\Models\User;
use App\Models\Vaccination;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::query()->where('role', User::ROLE_CLIENT)->first()
            ?? User::query()->where('role', User::ROLE_VETERINARY_STAFF)->first()
            ?? User::query()->where('role', User::ROLE_ADMIN)->first()
            ?? User::first();

        if (! $user) {
            return;
        }

        for ($i = 1; $i <= 5; $i++) {
            $pet = Pet::create([
                'owner_name' => "Owner {$i}",
                'contact_number' => '0912345678'.$i,
                'pet_name' => "Pet {$i}",
                'species' => 'Dog',
                'breed' => 'Mixed',
                'sex' => $i % 2 === 0 ? 'female' : 'male',
                'age_value' => 2,
                'age_type' => 'year',
                'user_id' => $user->id,
            ]);

            Appointment::create([
                'pet_id' => $pet->id,
                'type' => \App\Models\Appointment::TYPE_VACCINATION,
                'appointment_date' => now()->addDays($i),
                'purpose' => 'General Checkup',
                'status' => 'pending',
                'user_id' => $user->id,
            ]);

            Vaccination::create([
                'pet_id' => $pet->id,
                'pet_name' => $pet->pet_name,
                'owner_name' => $pet->owner_name,
                'contact_number' => $pet->contact_number,
                'vaccine_name' => 'Anti-Rabies',
                'date_given' => now()->subDays(10),
                'next_due_date' => now()->addMonths(6),
                'user_id' => $user->id,
            ]);
        }
    }
}
