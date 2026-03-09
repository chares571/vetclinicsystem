<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Pet;
use App\Models\Vaccination;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([AdminSeeder::class]);

        $client = \App\Models\User::query()
            ->where('role', \App\Models\User::ROLE_CLIENT)
            ->first();

        if (! $client) {
            $client = \App\Models\User::query()->updateOrCreate(
                ['email' => 'client@vetclinic.local'],
                [
                    'name' => 'Demo Client',
                    'password' => \Illuminate\Support\Facades\Hash::make('Client@123'),
                    'role' => \App\Models\User::ROLE_CLIENT,
                    'is_active' => true,
                    'must_change_password' => false,
                ]
            );
        }

        if (Pet::count() > 0) {
            return;
        }

        Pet::factory(20)->create(['user_id' => $client->id])
            ->each(function (Pet $pet) use ($client): void {
                Appointment::create([
                    'pet_id' => $pet->id,
                    'type' => \App\Models\Appointment::TYPE_VACCINATION,
                    'appointment_date' => now()->addDays(random_int(1, 30)),
                    'purpose' => 'Routine checkup',
                    'status' => 'pending',
                    'user_id' => $client->id,
                ]);

                Vaccination::create([
                    'pet_id' => $pet->id,
                    'pet_name' => $pet->pet_name,
                    'owner_name' => $pet->owner_name,
                    'contact_number' => $pet->contact_number,
                    'vaccine_name' => 'Anti-Rabies',
                    'date_given' => now()->subDays(random_int(1, 30)),
                    'next_due_date' => now()->addMonths(6),
                    'user_id' => $client->id,
                ]);
            });
    }
}
