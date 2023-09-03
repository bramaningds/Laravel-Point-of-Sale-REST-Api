<?php

namespace Tests\Feature\Api\Positive;

use Illuminate\Foundation\Testing\RefreshDatabase;

use Tests\TestCase;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;

class SaleItemTest extends TestCase
{
    use RefreshDatabase;

    public function test_index(): void
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->sequence(fn() => ['name' => 'bram'])->create();
        $products = Product::factory()->count(3)->create();

        $sale = Sale::factory()
                    ->for($user)
                    ->for($customer)
                    ->hasAttached($products, ['quantity' => 1, 'price' => 10000], 'items')
                    ->create();

        $response = $this->get("/api/sale/{$sale->id}/item");

        $response->assertStatus(200);
        $response->assertJson(function ($json) {
            $json->has('data', 3);
        });
    }

    public function test_search(): void
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->sequence(fn() => ['name' => 'bram'])->create();
        $products = Product::factory()->count(3)->create();

        $sale = Sale::factory()
                    ->for($user)
                    ->for($customer)
                    ->hasAttached($products, ['quantity' => 1, 'price' => 10000], 'items')
                    ->create();

        $response = $this->get('/api/sale?keyword=bram');

        $response->assertStatus(200);
        $response->assertJson(function ($json) use ($sale) {
            $json->hasAll(['meta', 'data', 'links']);
            $json->has('data', 1);
            $json->where('data.0.id', $sale->id);
        });
    }

    public function test_store(): void
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->sequence(fn() => ['name' => 'bram'])->create();
        $products = Product::factory()->count(2)->sequence(fn() => ['sellable' => 'Y', 'stock' => 10])->create();

        $sale = Sale::factory()
                    ->for($user)
                    ->for($customer)
                    ->hasAttached($products, ['quantity' => 1, 'price' => 10000], 'items')
                    ->create();

        $product = Product::factory()->sequence(fn() => ['sellable' => 'Y', 'stock' => 10])->create();

        $response = $this->postJson("/api/sale/{$sale->id}/item", [
            'id' => $product->id,
            'quantity' => 2
        ]);

        $response->assertStatus(201);
        $response->assertJson(function ($json) use ($product) {
            $json->has('data');
            $json->where('data.id', $product->id);
        });

        $this->assertDatabaseHas('sale_items', ['product_id' => $product->id]);
        $this->assertDatabaseCount('sale_items', 3);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 8]);
    }

    // public function test_show(): void
    // {
    //     $user = User::factory()->create();
    //     $customer = Customer::factory()->sequence(fn() => ['name' => 'bram'])->create();
    //     $products = Product::factory()->count(3)->create();
    //     $sale = Sale::factory()
    //                 ->for($user)
    //                 ->for($customer)
    //                 ->hasAttached($products, ['quantity' => 1, 'price' => 10000], 'items')
    //                 ->create();

    //     $response = $this->get("/api/sale/{$sale->id}");

    //     $response->assertStatus(200);
    //     $response->assertJson(function ($json) use ($sale) {
    //         $json->has('data');
    //         $json->has('data.id');
    //         $json->where('data.id', $sale->id);
    //     });
    // }

    // public function test_update(): void
    // {
    //     $user = User::factory()->create();
    //     $customer = Customer::factory()->create();
    //     $products = Product::factory()->count(3)->create();
    //     $sale = Sale::factory()
    //                 ->for($user)
    //                 ->for($customer)
    //                 ->hasAttached($products, ['quantity' => 1, 'price' => 10000], 'items')
    //                 ->create();

    //     $other_customer = Customer::factory()->create();

    //     $response = $this->putJson("/api/sale/{$sale->id}", [
    //         'customer_id' => $other_customer->id,
    //     ]);

    //     $response->assertStatus(200);
    //     $response->assertJson(function ($json) use ($other_customer) {
    //         $json->has('data');
    //         $json->has('data.id');
    //         $json->where('data.customer.name', $other_customer->name);
    //     });

    //     $this->assertDatabaseHas('sales', ['customer_id' => $other_customer->id]);
    // }

    // public function test_delete(): void
    // {
    //     $user = User::factory()->create();
    //     $customer = Customer::factory()->create();
    //     $products = Product::factory()->count(3)->create();
    //     $sale = Sale::factory()
    //                 ->for($user)
    //                 ->for($customer)
    //                 ->hasAttached($products, ['quantity' => 1, 'price' => 10000], 'items')
    //                 ->create();

    //     $response = $this->delete("/api/sale/{$sale->id}");

    //     $response->assertSuccessful();

    //     $this->assertSoftDeleted($sale);

    //     $products->each(function($product) {
    //         $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => $product->stock + 1]);
    //     });
    // }
}
