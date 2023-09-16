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
            'quantity' => fake()->numberBetween(1, 5),
            'price' => 500 * fake()->numberBetween(10, 50),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (PurchaseItem $purchase_item) {
            $purchase_item->load('product');
            $purchase_item->product->increment('stock', $purchase_item->quantity);
            $purchase_item->product->mutations()->create([
                'mutation_type' => 'purchase.store',
                'debet' => 0,
                'credit' => $purchase_item->quantity,
                'balance' => $purchase_item->product->stock
            ]);
        });
    }
}
