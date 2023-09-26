<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{

    private $count = 10;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->sequence(fn() => ['name' => 'Default'])->create();
        User::factory()->count($this->count - 1)->create();
    }

}
