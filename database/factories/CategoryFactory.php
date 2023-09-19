<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $words_count = fake()->randomElement([1,1,2,2,3]);

        return [
            'name' => fake()->words($words_count, true),
            'created_at' => fake()->dateTimeThisMonth(),
        ];
    }
}
