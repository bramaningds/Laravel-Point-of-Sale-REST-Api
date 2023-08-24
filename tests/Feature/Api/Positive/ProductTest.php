<?php

namespace Tests\Feature\Api\Positive;

use Tests\TestCase;

use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\Product;

class ProductTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_index(): void
    {
        $response = $this->get('/api/product');

        $response->assertStatus(200);
        $response->assertJson(function ($json) {
            $json->hasAll(['meta', 'data', 'links']);
            $json->has('data', 0);
        });
    }

    public function test_search(): void
    {
        $product = Product::factory()->sequence(fn() => ['name' => 'bram'])->create();

        $response = $this->get('/api/product?keyword=bram');

        $response->assertStatus(200);
        $response->assertJson(function ($json) {
            $json->hasAll(['meta', 'data', 'links']);
            $json->has('data', 1);
        });
    }

    public function test_store(): void
    {
        $response = $this->postJson('/api/product', [
            'name' => 'bram',
            'price' => 10000,
        ]);

        $response->assertStatus(201);
        $response->assertJson(function ($json) {
            $json->has('data');
            $json->has('data.id');
            $json->where('data.name', 'bram');
        });

        $this->assertDatabaseHas('products', ['name' => 'bram']);
        $this->assertModelExists(Product::where('name', 'bram')->first(['id']));
    }

    public function test_show(): void
    {
        $product = Product::factory()->create();

        $response = $this->get("/api/product/{$product->id}");

        $response->assertStatus(200);
        $response->assertJson(function ($json) use ($product) {
            $json->has('data');
            $json->has('data.id');
            $json->where('data.id', $product->id);
        });
    }

    public function test_update(): void
    {
        $product = Product::factory()->create();

        $response = $this->putJson("/api/product/{$product->id}", [
            'name' => 'bram',
        ]);

        $response->assertStatus(200);
        $response->assertJson(function ($json) use ($product) {
            $json->has('data');
            $json->has('data.id');
            $json->where('data.name', 'bram');
        });

        $this->assertDatabaseHas('products', ['name' => 'bram']);
    }

    public function test_delete(): void
    {
        $product = Product::factory()->create();

        $response = $this->delete("/api/product/{$product->id}");

        $response->assertSuccessful();

        $this->assertSoftDeleted($product);
    }
}
