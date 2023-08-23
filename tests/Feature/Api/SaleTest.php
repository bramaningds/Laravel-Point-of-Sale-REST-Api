<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;

use App\Models\Customer;

use Tests\TestCase;

/**
 * 
 * Basic positive tests (happy paths)
 * Extended positive testing with optional parameters
 * Negative testing with valid input
 * Negative testing with invalid input
 * Destructive testing
 * 
 */

class SaleTest extends TestCase
{

    public function test_browse()
    {
        $this->getJson('/api/sale')
             ->assertStatus(200)
             ->assertJson(function(AssertableJson $json) {
                $json->hasAll(['data', 'meta', 'links']);
             });

        $customer = Customer::find(1, ['name']);

        $this->getJson('/api/sale?keyword=' . $customer->name)
             ->assertStatus(200)
             ->assertJson(function(AssertableJson $json) use ($customer) {
                $json->first(function(AssertableJson $json) use ($customer) {
                    $json->assertJsonPath('customer.name', $customer->name);
                });
             });
    }
}
