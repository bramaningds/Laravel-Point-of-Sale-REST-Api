<?php

namespace Database\Factories;

use App\Models\SaleItem;
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
            $sale_item->load('product', 'sale');

            if ($sale_item->product->isNotStockable()) return;

            // Find the sale item id, it is not included in $sale_item
            $sale_item_id = SaleItem::query()
                ->where('sale_id', $sale_item->sale_id)
                ->where('product_id', $sale_item->product_id)
                ->max('id');

            // Find existing sale item
            $existing_sale_item = SaleItem::with('product')
                ->where('sale_id', $sale_item->sale_id)
                ->where('product_id', $sale_item->product_id)
                ->whereNot('id', $sale_item_id)
                ->first();

            if (! is_null($existing_sale_item)) {
                // Increase the stock of the product
                $existing_sale_item->product->increment('stock', $existing_sale_item->quantity);
                // 
                $existing_sale_item->update(['deleted_at' => now()]);
                // Create stock mutation record
                $existing_sale_item->product->mutations()->create([
                    'mutation_type' => 'sale.delete',
                    'reference_id' => $existing_sale_item->sale_id,
                    'debet' => $existing_sale_item->quantity,
                    'credit' => 0,
                    'balance' => $existing_sale_item->product->stock,
                ]);
            }

            // If product stock is lower than quantity then make adjustment to the stock
            if ($sale_item->product->stock < $sale_item->quantity) {
                // Set the stock adjustment by multiply it to 3
                $stock_adjsument = $sale_item->quantity * 3;
                // Increase the stock
                $sale_item->product->increment('stock', $stock_adjsument);
                // Create mutation
                $sale_item->product->mutations()->create([
                    'mutation_type' => 'adjustment',
                    'debet' => $stock_adjsument,
                    'credit' => 0,
                    'balance' => $sale_item->product->stock,
                ]);
            }

            // Decrease the stock of the product
            $sale_item->product->decrement('stock', $sale_item->quantity);
            // Create stock mutation record
            $sale_item->product->mutations()->create([
                'mutation_type' => 'sale.store',
                'reference_id' => $sale_item->sale_id,
                'debet' => 0,
                'credit' => $sale_item->quantity,
                'balance' => $sale_item->product->stock,
            ]);
        });
    }
}
