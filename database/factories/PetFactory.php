<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PetFactory extends Factory
{
    public function definition(): array
    {
        return [
            'owner_name' => fake()->name(),
            'contact_number' => fake()->phoneNumber(),
            'pet_name' => fake()->firstName(),
            'species' => fake()->randomElement(['Dog', 'Cat']),
            'breed' => fake()->randomElement(['Labrador', 'Persian', 'Bulldog', 'Mixed']),
            'sex' => fake()->randomElement(['male', 'female']),
            'age_value' => fake()->numberBetween(1, 20),
            'age_type' => 'year',
            'user_id' => User::factory(),
        ];
    }
}
