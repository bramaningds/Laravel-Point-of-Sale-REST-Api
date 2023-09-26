<?php

namespace Database\Seeders;

use App\Models\Supplier;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\User;
use DateInterval;
use DateTime;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PurchaseSeeder extends Seeder
{

    private $start_date = '2023-01-01';

    private $end_date = null;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $date_times = $this->generateDateTimes();

        $users = User::pluck('id');
        $suppliers = Supplier::pluck('id');
        $products = Product::where('stockable', 'Y')->where('purchasable', 'Y')->get();

        $purchase_items_chunks = array_map(function($date_times) use ($users, $suppliers) {
            return array_map(function($date_time) use ($users, $suppliers) {
                return [
                    'user_id' => $users->random(),
                    'supplier_id' => $suppliers->random(),
                    'discount' => fake()->randomElement([0, 0, 0, 0, 0, 5, 5, 10]),
                    'tax' => fake()->randomElement([0, 5, 11]),
                    'promo' => 500 * fake()->randomElement([0, 0, 0, 0, 0, 10, 20, 30]),
                    'created_at' => $date_time,
                    'updated_at' => $date_time,
                ];
            }, $date_times);
        }, array_chunk($date_times, 5000));

        foreach ($purchase_items_chunks as $purchase_items) DB::table('purchases')->insert($purchase_items);

        foreach (array_chunk(Purchase::doesntHave('items')->pluck('id')->toArray(), 1000) as $purchase_ids) {
            foreach (range(1, rand(1, 5)) as $count) {
                $purchase_items = array_map(function ($purchase_id) use ($products) {
                    $product = $products->random();

                    return [
                        'purchase_id' => $purchase_id,
                        'product_id' => $product->id,
                        'quantity' => 5,
                        'price' => $product->price + fake()->randomElement([-3, -1, 0, 0, 0, -1, 3]) * 500,
                    ];
                }, $purchase_ids);

                DB::table('purchase_items')->insert($purchase_items);
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
