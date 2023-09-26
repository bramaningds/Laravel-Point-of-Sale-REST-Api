<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{

    private $count = 10;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Customer::factory()->sequence(fn() => ['name' => 'guest', 'phone' => null, 'address' => null, 'email' => null])->create();
        Customer::factory()->count($this->count - 1)->create();
    }
}
