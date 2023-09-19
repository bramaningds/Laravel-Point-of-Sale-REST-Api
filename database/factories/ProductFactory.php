<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $yn = ['Y', 'Y', 'Y', 'Y', 'Y', 'N'];

        $stockable = fake()->randomElement($yn);
        $sellable = fake()->randomElement($yn);
        $purchasable = $stockable == 'Y' ? fake()->randomElement($yn) : 'N';

        return [
            'name' => fake()->unique()->words(fake()->numberBetween(2, 10), true),
            'description' => fake()->text(100),
            'price' => 500 * fake()->numberBetween(10, 50),
            'stockable' => $stockable,
            'stock' => 0,
            'sellable' => $sellable,
            'purchasable' => $purchasable,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Product $product) {
            if ($product->isNotStockable()) return;

            $product->increment('stock', 10);

            $product->mutations()->create([
                'mutation_type' => 'adjustment',
                'debet' => 10,
                'credit' => 0,
                'balance' => 10,
            ]);
        });
    }

}
