<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class PhoneNormalizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_accepts_local_0_prefix_and_stores_normalized()
    {
        $resp = $this->postJson('/auth/register', [
            'name' => 'Pengguna Baru',
            'email' => null,
            'phone' => '082232236630',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $resp->assertStatus(201)
            ->assertJsonStructure(['message', 'user' => ['id', 'name', 'email', 'phone', 'status', 'role']]);

        $userPhone = $resp->json('user.phone');
        $this->assertSame('6282232236630', $userPhone);

        $this->assertDatabaseHas('users', [
            'phone' => '6282232236630',
            'status' => User::STATUS_PENDING,
            'role' => User::ROLE_USER,
        ]);
    }

    public function test_api_login_with_0_prefix_succeeds_when_db_has_normalized_62()
    {
        $user = User::factory()->create([
            'name' => 'Tester 822',
            'email' => null,
            'phone' => '6282232236630',
            'password' => Hash::make('password123'),
            'status' => User::STATUS_ACTIVE,
            'role' => User::ROLE_USER,
        ]);

        $resp = $this->postJson('/api/auth/login', [
            'phone' => '082232236630',
            'password' => 'password123',
        ]);

        $resp->assertStatus(200)
            ->assertJsonStructure(['token', 'token_type', 'expires_in', 'user']);

        $this->assertSame('6282232236630', $resp->json('user.phone'));
    }

    public function test_profile_update_accepts_0_prefix_and_saves_normalized()
    {
        $user = User::factory()->active()->create([
            'password' => Hash::make('password123'),
            'phone' => '6281111111111',
        ]);

        $this->actingAs($user);

        $resp = $this->put('/profile', [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => '081234567890',
            // no password change
        ]);

        $resp->assertStatus(302);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'phone' => '6281234567890',
        ]);
    }

    public function test_admin_store_accepts_0_prefix_and_saves_normalized()
    {
        $admin = User::factory()->admin()->create([
            'phone' => '6289999999999',
            'password' => Hash::make('password123'),
        ]);

        $login = $this->postJson('/api/auth/login', [
            'phone' => '6289999999999',
            'password' => 'password123',
        ]);
        $login->assertStatus(200);
        $token = $login->json('token');

        $create = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/users', [
            'name' => 'User Normalisasi',
            'email' => null,
            'phone' => '081111111111',
            'password' => 'password123',
        ]);

        $create->assertStatus(201)
               ->assertJsonStructure(['message', 'user' => ['id', 'name', 'email', 'phone', 'status', 'role']]);

        $this->assertSame('6281111111111', $create->json('user.phone'));
        $this->assertDatabaseHas('users', [
            'phone' => '6281111111111',
            'status' => User::STATUS_PENDING,
            'role' => User::ROLE_USER,
        ]);
    }
}