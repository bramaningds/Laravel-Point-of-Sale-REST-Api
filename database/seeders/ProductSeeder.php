<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{

    private $count = 10;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all();

        Product::factory()->count($this->count)->sequence(function ($sequence) use ($categories) {
            return ['category_id' => $categories->random()->id];
        })->create();
    }
}
