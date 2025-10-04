<?php

namespace Database\Factories;

use App\Models\SearchHistory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SearchHistoryFactory extends Factory
{
    protected $model = SearchHistory::class;

    public function definition(): array
    {
        return [
            'user_id' => fn () => User::factory()->create()->id,
            'query' => $this->faker->words(2, true),
            'results_count' => $this->faker->numberBetween(0, 5),
        ];
    }
}