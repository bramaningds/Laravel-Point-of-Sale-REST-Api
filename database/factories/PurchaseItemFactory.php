<?php

namespace Database\Factories;

use App\Models\PurchaseItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PurchaseItem>
 */
class PurchaseItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'quantity' => fake()->numberBetween(1, 3),
            'price' => 500 * fake()->numberBetween(10, 50),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (PurchaseItem $purchase_item) {
            // Load the product relation
            $purchase_item->load('product');

            // Increase the stock
            $purchase_item->product->increment('stock', $purchase_item->quantity);
        });
    }
}
