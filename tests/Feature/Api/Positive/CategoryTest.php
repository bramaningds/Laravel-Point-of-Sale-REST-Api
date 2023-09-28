<?php

namespace Tests\Feature\Api\Positive;

use App\Models\Category;
use Tests\TestCase;

class CategoryTest extends TestCase
{

    public function test_index(): void
    {
        $category = Category::factory()->create();

        $response = $this->get('/api/category');

        $response->assertStatus(200);
        $response->assertJson(function ($json) use ($category) {
            $json->hasAll(['meta', 'data', 'links']);
            $json->has('data', 1);
            $json->where('data.0.name', $category->name);
        });
    }

    public function test_search(): void
    {
        $category = Category::factory()->sequence(fn() => ['name' => 'bram'])->create();

        $response = $this->get('/api/category?keyword=bram');

        $response->assertStatus(200);
        $response->assertJson(function ($json) use ($category) {
            $json->hasAll(['meta', 'data', 'links']);
            $json->has('data', 1);
            $json->where('data.0.name', $category->name);
        });
    }

    public function test_store(): void
    {
        $response = $this->postJson('/api/category', ['name' => 'bram']);

        $response->assertStatus(201);
        $response->assertJson(function ($json) {
            $json->has('data');
            $json->has('data.id');
            $json->where('data.name', 'bram');
        });

        $this->assertDatabaseHas('categories', ['name' => 'bram']);
        $this->assertModelExists(Category::where('name', 'bram')->first());
    }

    public function test_show(): void
    {
        $category = Category::factory()->create();

        $response = $this->get("/api/category/{$category->id}");

        $response->assertStatus(200);
        $response->assertJson(function ($json) use ($category) {
            $json->has('data');
            $json->has('data.id');
            $json->where('data.id', $category->id);
        });
    }

    public function test_update(): void
    {
        $category = Category::factory()->create();

        $response = $this->putJson("/api/category/{$category->id}", ['name' => 'bram']);

        $response->assertStatus(200);
        $response->assertJson(function ($json) use ($category) {
            $json->has('data');
            $json->has('data.id');
            $json->where('data.name', 'bram');
        });

        $this->assertDatabaseHas('categories', ['name' => 'bram']);
    }

    public function test_delete(): void
    {
        $category = Category::factory()->create();

        $response = $this->delete("/api/category/{$category->id}");
        $response->assertSuccessful();

        $this->assertSoftDeleted($category);
    }
}
