<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            // Email opsional dan unik jika diisi
            'email' => function () {
                // Email opsional, unik jika diisi; hindari chaining pada null dari optional()
                return fake()->boolean(70) ? fake()->unique()->safeEmail() : null;
            },
            // Phone sebagai kredensial utama (format E.164, contoh Indonesia +62)
            'phone' => fake()->unique()->numerify('+628##########'),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            // Default sesuai spesifikasi
            'status' => 'pending',
            'role' => 'user',
            // Verifikasi email tidak digunakan
            'email_verified_at' => null,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * State: user aktif.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * State: user banned.
     */
    public function banned(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'banned',
        ]);
    }

    /**
     * State: admin aktif.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
            'status' => 'active',
        ]);
    }
}
