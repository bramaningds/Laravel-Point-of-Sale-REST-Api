<?php

namespace Tests\Feature\Api\Positive;

use App\Models\Supplier;
use Tests\TestCase;

class SupplierTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_index(): void
    {
        $response = $this->get('/api/supplier');

        $response->assertStatus(200);
        $response->assertJson(function ($json) {
            $json->hasAll(['meta', 'data', 'links']);
            $json->has('data', 0);
        });
    }

    public function test_search(): void
    {
        $supplier = Supplier::factory()->sequence(fn() => ['name' => 'bram'])->create();

        $response = $this->get('/api/supplier?keyword=bram');

        $response->assertStatus(200);
        $response->assertJson(function ($json) {
            $json->hasAll(['meta', 'data', 'links']);
            $json->has('data', 1);
        });

        $supplier->forceDelete();
    }

    public function test_store(): void
    {
        $response = $this->postJson('/api/supplier', [
            'name' => 'bram',
        ]);

        $response->assertStatus(201);
        $response->assertJson(function ($json) {
            $json->has('data');
            $json->has('data.id');
            $json->where('data.name', 'bram');
        });

        $this->assertDatabaseHas('suppliers', ['name' => 'bram']);
        $this->assertModelExists(Supplier::where('name', 'bram')->first(['id']));

        Supplier::where('name', 'bram')->forceDelete();
    }

    public function test_show(): void
    {
        $supplier = Supplier::factory()->create();

        $response = $this->get("/api/supplier/{$supplier->id}");

        $response->assertStatus(200);
        $response->assertJson(function ($json) use ($supplier) {
            $json->has('data');
            $json->has('data.id');
            $json->where('data.id', $supplier->id);
        });

        $supplier->forceDelete();
    }

    public function test_update(): void
    {
        $supplier = Supplier::factory()->create();

        $response = $this->putJson("/api/supplier/{$supplier->id}", [
            'name' => 'bram',
        ]);

        $response->assertStatus(200);
        $response->assertJson(function ($json) use ($supplier) {
            $json->has('data');
            $json->has('data.id');
            $json->where('data.name', 'bram');
        });

        $this->assertDatabaseHas('suppliers', ['name' => 'bram']);

        $supplier->forceDelete();
    }

    public function test_delete(): void
    {
        $supplier = Supplier::factory()->create();

        $response = $this->delete("/api/supplier/{$supplier->id}");

        $response->assertSuccessful();

        $this->assertSoftDeleted($supplier);

        $supplier->forceDelete();

        $this->assertModelMissing($supplier);
    }
}
