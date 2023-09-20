<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Purchase>
 */
class PurchaseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'discount' => fake()->randomElement([0,0,0,0,0,5,5,10]),
            'tax' => fake()->randomElement([0,5,11]),
            'promo' => 500 * fake()->randomElement([0,0,0,0,0,10,20,30]),
            'created_at' => fake()->dateTimeThisMonth(),
        ];
    }
}
