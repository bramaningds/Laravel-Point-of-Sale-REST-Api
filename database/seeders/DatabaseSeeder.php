<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{

    protected $userCount = 10;
    protected $customerCount = 10;
    protected $supplierCount = 10;
    protected $categoryCount = 5;
    protected $productCount = 20;
    protected $saleCount = 100;
    protected $purchaseCount = 100;

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
        // create users
        $users = User::factory()->count($this->userCount)->create();

        // create customers
        $customers = Customer::factory()->count($this->customerCount)->create();

        // create customers
        $suppliers = Supplier::factory()->count($this->supplierCount)->create();

        // create categories
        $categories = Category::factory()->count($this->categoryCount)->create();

        // create products
        $products = Product::factory()->count($this->productCount)->sequence(function ($sequence) use ($categories) {
            return ['category_id' => $categories->random()->id];
        })->create();

        // create purchases
        $purchases = Purchase::factory()->count($this->purchaseCount)->sequence(function ($sequence) use ($users, $suppliers) {
            return [
                'user_id' => $users->random()->id,
                'supplier_id' => $suppliers->random()->id,
            ];
        })->create();

        // foreach sale create some purchase items
        $purchase_items = $purchases->map(function($purchase) use ($products) {
            return PurchaseItem::factory()->count($this->generateItemCount())->sequence(function ($sequence) use ($purchase, $products) {
                $product = $products->random();

                return [
                    'purchase_id' => $purchase->id,
                    'product_id' => $product->id,
                    'price' => $product->price,
                    'created_at' => $purchase->created_at,
                    'updated_at' => $purchase->created_at,
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
        $sale_items = $sales->map(function($sale) use ($products) {
            return SaleItem::factory()->count($this->generateItemCount())->sequence(function ($sequence) use ($sale, $products) {
                $product = $products->random();
                return [
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'price' => $product->price,
                    'created_at' => $sale->created_at,
                    'updated_at' => $sale->created_at,
                ];
            })->create();
        });
    }
}
