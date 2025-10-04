<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Hadith>
 */
class HadithFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'chapter_id' => \App\Models\Chapter::factory(),
            'arabic_text' => 'نص الحديث باللغة العربية هنا...',
            'translation' => fake()->paragraph(),
            'interpretation' => fake()->paragraphs(3, true),
            'narration_source' => 'HR. ' . fake()->randomElement(['Al-Bukhari', 'Muslim', 'Abu Dawud', 'Tirmidzi', 'Nasa\'i', 'Ibn Majah']),
            'hadith_number' => fake()->numberBetween(1, 100),
        ];
    }
}