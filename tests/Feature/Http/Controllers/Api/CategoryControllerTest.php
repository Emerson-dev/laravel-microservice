<?php

namespace Tests\Feature\Http\Controllers\Api;

use Tests\TestCase;
use App\Models\Category;
use Illuminate\Support\Facades\Lang;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\Traits\TestValidations;

class CategoryControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidations;

    public function testIndex()
    {

        $category = factory(Category::class)->create();

        /** @var TestResponse $response */
        $response = $this->get(route('categories.index'));

        $response
            ->assertStatus(200)
            ->assertJson([$category->toArray()]);
    }

    public function testShow()
    {

        $category = factory(Category::class)->create();

        /** @var TestResponse $response */
        $response = $this->get(route('categories.show', ['category' => $category->id]));

        $response
            ->assertStatus(200)
            ->assertJson($category->toArray());
    }

    public function testInvalidationData()
    {
        $response = $this->json('POST', route('categories.store'), []);
        $this->assertinvalidationRequired($response);

        $response = $this->json('POST', route('categories.store'), [
            'name' => str_repeat('a', 256),
            'is_active' => 'a'
        ]);
        $this->assertInvalidationMax($response);
        $this->assertInvalidationBoolean($response);

        $category = factory(Category::class)->create();

        $response = $this->json(
            'PUT',
            route('categories.update', ['category' => $category->id]),
            ['name' => str_repeat('a', 256), 'is_active' => 'a']
        );
        $this->assertInvalidationMax($response);
        $this->assertInvalidationBoolean($response);

        $response = $this->json('PUT', route('categories.update', ['category' => $category->id]), []);
        $this->assertinvalidationRequired($response);
    }

    protected function assertinvalidationRequired(TestResponse $response)
    {

        $this->assertInValidationFields($response, ['name'], 'required');
        $response
            ->assertJsonMissingValidationErrors(['is_active']);
    }

    protected function assertInvalidationMax(TestResponse $response)
    {
        $this->assertInValidationFields($response, ['name'], 'max.string', ['max' => 255]);
        $response
            ->assertJsonValidationErrors(['name']);
    }

    protected function assertInvalidationBoolean(TestResponse $response)
    {
        $this->assertInValidationFields($response, ['is_active'], 'boolean');
        $response
            ->assertJsonValidationErrors(['is_active']);
    }

    public function testStore()
    {
        $response = $this->json('POST', route('categories.store'), [
            'name' => 'test'
        ]);
        $id = $response->json('id');
        $category = Category::find($id);

        $response
            ->assertStatus(201)
            ->assertJson($category->toArray());
        $this->assertTrue($response->json('is_active'));
        $this->assertNull($response->json('description'));

        $response = $this->json('POST', route('categories.store'), [
            'name' => 'test',
            'is_active' => false,
            'description' => 'test'
        ]);

        $response
            ->assertJsonFragment([
                'is_active' => false,
                'description' => 'test'
            ]);
    }

    public function testUpdate()
    {
        $category = factory(Category::class)->create([
            'description' => 'description',
            'is_active' => false
        ]);
        $response = $this->json('PUT', route('categories.update', ['category' => $category->id]), [
            'name' => 'test',
            'description' => 'test',
            'is_active' => true
        ]);
        $id = $response->json('id');
        $category = Category::find($id);

        $response
            ->assertStatus(200)
            ->assertJson($category->toArray())
            ->assertJsonFragment([
                'description' => 'test',
                'is_active' => true
            ]);

        $response = $this->json('PUT', route('categories.update', ['category' => $category->id]), [
            'name' => 'test',
            'description' => ''
        ]);

        $response
            ->assertJsonFragment([
                'description' => null
            ]);
    }

    public function testDelete()
    {
        $category = factory(Category::class)->create();

        $response = $this->json('DELETE', route('categories.destroy', ['category' => $category->id]), []);

        $response
            ->assertStatus(204);
        $this->assertNull(Category::find($category->id));
        $this->assertNotNull(Category::withTrashed()->find($category->id));
    }
}
