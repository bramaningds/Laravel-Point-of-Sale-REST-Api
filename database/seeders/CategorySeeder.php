<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{

    private $count = 10;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::factory()->sequence(fn() => ['name' => 'Default'])->create();
        Category::factory()->count($this->count - 1)->create();
    }
}
