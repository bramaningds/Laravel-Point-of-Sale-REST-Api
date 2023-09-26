<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    private function generateItemCount()
    {
        $options = [1, 1, 2, 2, 2, 3, 3, 4, 5, 6, 7];
        return $options[rand(0, count($options) - 1)];
    }

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            SupplierSeeder::class,
            CustomerSeeder::class,
            PurchaseSeeder::class,
            SaleSeeder::class,
        ]);

        return;

        // foreach sale create some purchase items
        $purchase_items = $purchases->map(function ($purchase) use ($products) {
            return PurchaseItem::factory()->count($this->generateItemCount())->sequence(function ($sequence) use ($purchase, $products) {
                $product = $products->where('stockable', 'Y')->where('purchasable', 'Y')->random();

                return [
                    'purchase_id' => $purchase->id,
                    'product_id' => $product->id,
                    'quantity' => 10 * fake()->randomElement([1, 2, 3]),
                    'price' => $product->price + fake()->randomElement([-10, -5, -3, -1, -1, -1, 0]) * 500,
                ];

            })->create();
        });

        // create sales
        $sales = Sale::factory()->count($this->saleCount)->sequence(function ($sequence) use ($users, $customers) {
            return [
                'user_id' => $users->random()->id,
                'customer_id' => $customers->random()->id,
            ];
        })->create();

        // create sale_items
        $sale_items = $sales->map(function ($sale) use ($products) {
            return SaleItem::factory()->count($this->generateItemCount())->sequence(function ($sequence) use ($sale, $products) {
                $quantity = fake()->numberBetween(1, 5);
                $product = $products->filter(fn($product) => $product->sellable == 'Y')->random();
                $price = $product->price + fake()->randomElement([-3, -1, 0, 0, 0, -1, 3]) * 500;

                return [
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price' => $price,
                ];
            })->create();
        });
    }
}
