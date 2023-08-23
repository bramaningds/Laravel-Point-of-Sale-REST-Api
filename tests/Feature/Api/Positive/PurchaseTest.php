<?php

namespace Tests\Feature\Api\Positive;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

use Illuminate\Testing\Fluent\AssertableJson;

use Tests\TestCase;

class PurchaseTest extends TestCase
{
    public function test_browse()
    {
        $this->getJson('/api/purchase')
             ->assertStatus(200)
             ->assertJson(function(AssertableJson $json) {
                $json->hasAll(['data', 'meta', 'links']);
             });
    }
}
