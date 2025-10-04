<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Chapter>
 */
class ChapterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => 'Bab ' . fake()->numberBetween(1, 56),
            'description' => fake()->paragraph(),
            'chapter_number' => fake()->unique()->numberBetween(1, 56),
        ];
    }
}