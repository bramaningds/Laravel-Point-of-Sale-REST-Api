<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use DateInterval;
use DateTime;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::pluck('id');
        $customers = Customer::pluck('id');
        $products = Product::where('sellable', 'Y')->get();

        foreach (array_chunk($this->generateDateTimes(), 5000) as $date_times) {
            DB::table('sales')->insert(array_map(function ($date_time) use ($users, $customers) {
                return [
                    'user_id' => $users->random(),
                    'customer_id' => $customers->random(),
                    'discount' => fake()->randomElement([0, 0, 0, 0, 0, 5, 5, 10]),
                    'tax' => fake()->randomElement([0, 5, 11]),
                    'promo' => 500 * fake()->randomElement([0, 0, 0, 0, 0, 10, 20, 30]),
                    'created_at' => $date_time,
                    'updated_at' => $date_time,
                ];
            }, $date_times));
        }

        foreach (array_chunk(Sale::doesntHave('items')->pluck('id')->toArray(), 1000) as $sale_ids) {
            foreach (range(1, rand(1, 5)) as $count) {
                $sale_items = array_map(function ($sale_id) use ($products) {
                    $product = $products->random();

                    return [
                        'sale_id' => $sale_id,
                        'product_id' => $product->id,
                        'quantity' => fake()->numberBetween(1, 5),
                        'price' => $product->price + fake()->randomElement([-3, -1, 0, 0, 0, -1, 3]) * 500,
                    ];
                }, $sale_ids);

                DB::table('sale_items')->insert($sale_items);
            }
        }
    }

    private function generateDateTimes()
    {
        $date_times = [];

        $start_date = date_create('2023-01-01');
        $end_date = date_create();

        foreach (range($start_date->format('z'), $end_date->format('z')) as $z) {
            foreach (range(0, rand(7, 12)) as $per_day_count) {
                foreach (range(0, rand(2, 5)) as $per_hour_count) {
                    $hour = rand(7, 22);
                    $minute = rand(0, 59);
                    $second = rand(0, 59);

                    $date = clone $start_date;
                    $date->add(new DateInterval("P{$z}D"));
                    $date->add(new DateInterval("PT{$hour}H"));
                    $date->add(new DateInterval("PT{$minute}M"));
                    $date->add(new DateInterval("PT{$second}S"));

                    $date_times[] = $date->format('Y-m-d H:i:s');
                }
            }
        }

        asort($date_times, SORT_STRING);

        return $date_times;
    }
}
