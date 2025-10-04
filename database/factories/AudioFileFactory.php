<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AudioFile>
 */
class AudioFileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'hadith_id' => \App\Models\Hadith::factory(),
            'file_path' => 'audio/' . fake()->uuid() . '.mp3',
            'duration' => fake()->numberBetween(60, 600), // 1-10 minutes in seconds
            'file_size' => fake()->numberBetween(1024000, 10240000), // 1-10 MB in bytes
        ];
    }
}