<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()
            ->whereIn('email', ['admin@vetclinic.local', 'superadmin@vetclinic.local'])
            ->first();

        if ($admin) {
            $admin->update([
                'email' => 'admin@vetclinic.local',
                'name' => 'Admin',
                'password' => Hash::make('Admin@123'),
                'role' => User::ROLE_ADMIN,
                'is_active' => true,
                'must_change_password' => true,
            ]);
        } else {
            User::create([
                'email' => 'admin@vetclinic.local',
                'name' => 'Admin',
                'password' => Hash::make('Admin@123'),
                'role' => User::ROLE_ADMIN,
                'is_active' => true,
                'must_change_password' => true,
            ]);
        }

        $staff = User::query()
            ->whereIn('email', ['staff@vetclinic.local', 'vetstaff@vetclinic.local'])
            ->first();

        if ($staff) {
            $staff->update([
                'email' => 'staff@vetclinic.local',
                'name' => 'Veterinary Staff',
                'password' => Hash::make('Staff@123'),
                'role' => User::ROLE_VETERINARY_STAFF,
                'is_active' => true,
                'must_change_password' => true,
            ]);
        } else {
            User::create([
                'email' => 'staff@vetclinic.local',
                'name' => 'Veterinary Staff',
                'password' => Hash::make('Staff@123'),
                'role' => User::ROLE_VETERINARY_STAFF,
                'is_active' => true,
                'must_change_password' => true,
            ]);
        }
    }
}
