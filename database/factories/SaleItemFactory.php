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
            $sale_item->load('product');

            // If product stock is lower than quantity then adjust the stock
            if ($sale_item->product->stock < $sale_item->quantity) {
                $stock_adjsument = $sale_item->quantity * 3;

                $sale_item->product->increment('stock', $stock_adjsument);
                $sale_item->product->mutations()->create([
                    'mutation_type' => 'adjustment',
                    'debet' => $stock_adjsument,
                    'credit' => 0,
                    'balance' => $sale_item->product->stock,
                ]);
            }

            // Decrease the stock of the product
            $sale_item->product->decrement('stock', $sale_item->quantity);
            $sale_item->product->mutations()->create([
                'mutation_type' => 'sale.store',
                'debet' => 0,
                'credit' => $sale_item->quantity,
                'balance' => $sale_item->product->stock,
            ]);

            // Remove sale_item record which has same sale_id, product_id
            $older_sale_items = SaleItem::query()
                ->where('sale_id', $sale_item->sale_id)
                ->where('product_id', $sale_item->product_id)
                ->get();
            $older_sale_items->pop();
            $older_sale_items->each(function (SaleItem $older_sale_item) {
                $older_sale_item->load('product');
                $older_sale_item->update(['deleted_at' => now()]);
                $older_sale_item->product->increment('stock', $older_sale_item->quantity);
                $older_sale_item->product->mutations()->create([
                    'mutation_type' => 'sale.delete',
                    'debet' => $older_sale_item->quantity,
                    'credit' => 0,
                    'balance' => $older_sale_item->product->stock,
                ]);
            });
        });
    }
}
