<?php
/**
 * ============================================================
 * @module      Auth
 * @layer       Test > Feature
 * @file        LoginTest.php
 * @path        app/Modules/Auth/Tests/Feature/LoginTest.php
 * @description Test HTTP endpoint login: sukses, gagal validasi,
 *              kredensial salah, logout, dan ambil current user.
 * @covers      app/Modules/Auth/Controllers/AuthController.php
 * @covers      app/Modules/Auth/Services/AuthService.php
 * @author      [Nama Developer]
 * @since       v1.0.0
 * ============================================================
 */

use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('logs in successfully with valid credentials', function () {
    $user = User::factory()->create([
        'email' => 'staff@chleo.test',
        'password' => Hash::make('Password123!'),
    ]);

    $response = $this->withHeader('Referer', 'http://localhost')->postJson('/api/v1/auth/login', [
        'email' => 'staff@chleo.test',
        'password' => 'Password123!',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure(['success', 'message', 'data' => ['id', 'name', 'email']])
        ->assertJsonPath('data.email', $user->email);

    $this->assertAuthenticatedAs($user);
});

it('returns 422 when email is missing', function () {
    $this->postJson('/api/v1/auth/login', ['password' => 'Password123!'])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

it('returns 422 when credentials are wrong', function () {
    User::factory()->create([
        'email' => 'staff@chleo.test',
        'password' => Hash::make('Password123!'),
    ]);

    $this->postJson('/api/v1/auth/login', [
        'email' => 'staff@chleo.test',
        'password' => 'salah-password',
    ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['email']);

    $this->assertGuest();
});

it('returns the authenticated user on /auth/me', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->getJson('/api/v1/auth/me')
        ->assertStatus(200)
        ->assertJsonPath('data.email', $user->email);
});

it('returns 401 from /auth/me when unauthenticated', function () {
    $this->getJson('/api/v1/auth/me')->assertStatus(401);
});

it('logs out the authenticated user', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->withHeader('Referer', 'http://localhost')
        ->postJson('/api/v1/auth/logout')
        ->assertStatus(200);

    $this->assertGuest('web');
});
