<?php

namespace Database\Factories;

use App\Models\SaleItem;
use App\Models\User;
use App\Models\Supplier;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SaleItem>
 */
class SaleItemFactory extends Factory
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
        return $this->afterCreating(function (SaleItem $sale_item) {
            // Load the relation
            $sale_item->loadMissing('product');
            // Skip decrement if not stockable
            if ($sale_item->product->isNotStockable()) return;

            // Decrease the stock of the product
            $sale_item->product->decrement('stock', $sale_item->quantity);
        });
    }
}
