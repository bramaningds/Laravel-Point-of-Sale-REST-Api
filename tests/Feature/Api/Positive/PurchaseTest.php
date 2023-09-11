<?php

namespace Tests\Feature\Api\Positive;

use Illuminate\Foundation\Testing\RefreshDatabase;

use Tests\TestCase;

use App\Models\Category;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\User;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_index(): void
    {
        $response = $this->get('/api/purchase');

        $response->assertStatus(200);
        $response->assertJson(function ($json) {
            $json->hasAll(['meta', 'data', 'links']);
            $json->has('data', 0);
        });
    }

    public function test_search(): void
    {
        $user = User::factory()->create();
        $supplier = Supplier::factory()->sequence(fn() => ['name' => 'bram'])->create();
        $products = Product::factory()->count(3)->for(Category::factory())->create();

        $purchase = Purchase::factory()
                    ->for($user)
                    ->for($supplier)
                    ->hasAttached($products, ['quantity' => 1, 'price' => 10000], 'items')
                    ->create();

        $response = $this->get('/api/purchase?keyword=bram');

        $response->assertStatus(200);
        $response->assertJson(function ($json) use ($purchase) {
            $json->hasAll(['meta', 'data', 'links']);
            $json->has('data', 1);
            $json->where('data.0.id', $purchase->id);
        });
    }

    public function test_store(): void
    {
        $user = User::factory()->create();
        $supplier = Supplier::factory()->sequence(fn() => ['name' => 'bram'])->create();
        $products = Product::factory()->count(3)->for(Category::factory())->sequence(fn() => ['purchasable' => 'Y', 'stock' => 10])->create();

        $response = $this->postJson('/api/purchase', [
            'user_id' => $user->id,
            'supplier_id' => $supplier->id,
            'items' => $products->map(fn($product) => [
                'id' => $product->id,
                'quantity' => 2,
                'price' => $product->price
            ])
        ]);

        $response->assertStatus(201);
        $response->assertJson(function ($json) use ($user, $supplier, $products) {
            $json->has('data');
            $json->where('data.user.name', $user->name);
            $json->where('data.supplier.name', $supplier->name);
            $json->where('data.items.0.name', $products[0]->name);
        });

        $this->assertDatabaseHas('purchases', ['user_id' => $user->id, 'supplier_id' => $supplier->id]);
        $this->assertDatabaseHas('purchase_items', ['product_id' => $products[0]->id]);
        $this->assertDatabaseCount('purchase_items', 3);

        $products->each(function($product) {
            $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 8]);
        });
    }

    public function test_show(): void
    {
        $user = User::factory()->create();
        $supplier = Supplier::factory()->sequence(fn() => ['name' => 'bram'])->create();
        $products = Product::factory()->count(3)->for(Category::factory())->create();
        $purchase = Purchase::factory()
                    ->for($user)
                    ->for($supplier)
                    ->hasAttached($products, ['quantity' => 1, 'price' => 10000], 'items')
                    ->create();

        $response = $this->get("/api/purchase/{$purchase->id}");

        $response->assertStatus(200);
        $response->assertJson(function ($json) use ($purchase) {
            $json->has('data');
            $json->has('data.id');
            $json->where('data.id', $purchase->id);
        });
    }

    public function test_update(): void
    {
        $user = User::factory()->create();
        $supplier = Supplier::factory()->create();
        $products = Product::factory()->count(3)->for(Category::factory())->create();
        $purchase = Purchase::factory()
                    ->for($user)
                    ->for($supplier)
                    ->hasAttached($products, ['quantity' => 1, 'price' => 10000], 'items')
                    ->create();

        $other_supplier = Supplier::factory()->create();

        $response = $this->putJson("/api/purchase/{$purchase->id}", [
            'supplier_id' => $other_supplier->id,
        ]);

        $response->assertStatus(200);
        $response->assertJson(function ($json) use ($other_supplier) {
            $json->has('data');
            $json->has('data.id');
            $json->where('data.supplier.name', $other_supplier->name);
        });

        $this->assertDatabaseHas('purchases', ['supplier_id' => $other_supplier->id]);
    }

    public function test_delete(): void
    {
        $user = User::factory()->create();
        $supplier = Supplier::factory()->create();
        $products = Product::factory()->count(3)->for(Category::factory())->create();
        $purchase = Purchase::factory()
                    ->for($user)
                    ->for($supplier)
                    ->hasAttached($products, ['quantity' => 1, 'price' => 10000], 'items')
                    ->create();

        $response = $this->delete("/api/purchase/{$purchase->id}");

        $response->assertSuccessful();

        $this->assertSoftDeleted($purchase);

        $products->each(function($product) {
            $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => $product->stock + 1]);
        });
    }
}
