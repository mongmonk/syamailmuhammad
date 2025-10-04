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
            'footnotes' => fake()->paragraphs(2, true),
            'hadith_number' => fake()->numberBetween(1, 100),
        ];
    }
}