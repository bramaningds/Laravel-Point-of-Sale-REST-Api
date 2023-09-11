<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{

    protected $yn = ['Y', 'Y', 'Y', 'Y', 'Y', 'N'];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(fake()->numberBetween(2, 10), true),
            'description' => fake()->text(100),
            'price' => 500 * fake()->numberBetween(10, 50),
            'stock' => 1000,
            'sellable' => $this->yn[rand(0, count($this->yn)-1)],
            'purchasable' => $this->yn[rand(0, count($this->yn)-1)],
        ];
    }
}
