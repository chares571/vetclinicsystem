<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('users flagged for password reset are redirected after login', function () {
    $user = User::factory()->create([
        'password' => Hash::make('password'),
        'must_change_password' => true,
    ]);

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('password.force.edit'));
});

test('users flagged for password reset cannot access protected routes', function () {
    $user = User::factory()->create([
        'must_change_password' => true,
    ]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertRedirect(route('password.force.edit'));
});

test('forced password update clears reset flag and redirects to role dashboard', function () {
    $user = User::factory()->create([
        'password' => Hash::make('password'),
        'must_change_password' => true,
        'role' => User::ROLE_ADMIN,
    ]);

    $response = $this->actingAs($user)->put(route('password.force.update'), [
        'current_password' => 'password',
        'password' => 'Password@12345',
        'password_confirmation' => 'Password@12345',
    ]);

    $response->assertRedirect(route('admin.dashboard'));
    expect($user->fresh()->must_change_password)->toBeFalse();
});
