<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{

    private $count = 10;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Supplier::factory()->sequence(fn() => ['name' => 'Adjustment', 'phone' => null, 'address' => null, 'email' => null])->create();
        Supplier::factory()->count($this->count - 1)->create();
    }
}
