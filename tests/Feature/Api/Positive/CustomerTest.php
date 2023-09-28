<?php

namespace Tests\Feature\Api\Positive;

use App\Models\Customer;
use Tests\TestCase;

class CustomerTest extends TestCase
{

    public function test_index(): void
    {
        $response = $this->get('/api/customer');

        $response->assertStatus(200);
        $response->assertJson(function ($json) {
            $json->hasAll(['meta', 'data', 'links']);
            $json->has('data', 0);
        });
    }

    public function test_search(): void
    {
        $customer = Customer::factory()->sequence(fn() => ['name' => 'bram'])->create();

        $response = $this->get('/api/customer?keyword=bram');

        $response->assertStatus(200);
        $response->assertJson(function ($json) {
            $json->hasAll(['meta', 'data', 'links']);
            $json->has('data', 1);
        });

        $customer->forceDelete();
    }

    public function test_store(): void
    {
        $response = $this->postJson('/api/customer', [
            'name' => 'bram',
        ]);

        $response->assertStatus(201);
        $response->assertJson(function ($json) {
            $json->has('data');
            $json->has('data.id');
            $json->where('data.name', 'bram');
        });

        $this->assertDatabaseHas('customers', ['name' => 'bram']);
        $this->assertModelExists(Customer::where('name', 'bram')->first(['id']));

        Customer::where('name', 'bram')->forceDelete();
    }

    public function test_show(): void
    {
        $customer = Customer::factory()->create();

        $response = $this->get("/api/customer/{$customer->id}");

        $response->assertStatus(200);
        $response->assertJson(function ($json) use ($customer) {
            $json->has('data');
            $json->has('data.id');
            $json->where('data.id', $customer->id);
        });

        $customer->forceDelete();
    }

    public function test_update(): void
    {
        $customer = Customer::factory()->create();

        $response = $this->putJson("/api/customer/{$customer->id}", [
            'name' => 'bram',
        ]);

        $response->assertStatus(200);
        $response->assertJson(function ($json) use ($customer) {
            $json->has('data');
            $json->has('data.id');
            $json->where('data.name', 'bram');
        });

        $this->assertDatabaseHas('customers', ['name' => 'bram']);

        $customer->forceDelete();
    }

    public function test_delete(): void
    {
        $customer = Customer::factory()->create();

        $response = $this->delete("/api/customer/{$customer->id}");

        $response->assertSuccessful();

        $this->assertSoftDeleted($customer);

        $customer->forceDelete();

        $this->assertModelMissing($customer);
    }
}
