<?php

namespace Tests\Feature\Api\Positive;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

use Illuminate\Testing\Fluent\AssertableJson;

use Tests\TestCase;

class SaleTest extends TestCase
{
    public function test_browse()
    {
        $this->getJson('/api/sale')
             ->assertStatus(200)
             ->assertJson(function(AssertableJson $json) {
                $json->hasAll(['data', 'meta', 'links']);
             });
    }
}
