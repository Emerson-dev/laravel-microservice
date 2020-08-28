<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Category;
use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Support\Facades\Lang;
use Tests\Traits\TestValidations;

class GenreControllerTest extends TestCase
{
    use DatabaseMigrations, TestValidations;

    public function testIndex()
    {

        $genre = factory(Genre::class)->create();

        /** @var TestResponse $response */
        $response = $this->get(route('genres.index'));

        $response
            ->assertStatus(200)
            ->assertJson([$genre->toArray()]);
    }

    public function testShow()
    {

        $genre = factory(Genre::class)->create();

        /** @var TestResponse $response */
        $response = $this->get(route('genres.show', ['genre' => $genre->id]));

        $response
            ->assertStatus(200)
            ->assertJson($genre->toArray());
    }

    public function testInvalidationData()
    {
        $response = $this->json('POST', route('genres.store'), []);
        $this->assertinvalidationRequired($response);

        $response = $this->json('POST', route('genres.store'), [
            'name' => str_repeat('a', 256),
            'is_active' => 'a'
        ]);
        $this->assertInvalidationMax($response);
        $this->assertInvalidationBoolean($response);

        $genre = factory(Genre::class)->create();

        $response = $this->json(
            'PUT',
            route('genres.update', ['genre' => $genre->id]),
            ['name' => str_repeat('a', 256), 'is_active' => 'a']
        );
        $this->assertInvalidationMax($response);
        $this->assertInvalidationBoolean($response);

        $response = $this->json('PUT', route('genres.update', ['genre' => $genre->id]), []);
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
        $response = $this->json('POST', route('genres.store'), [
            'name' => 'test'
        ]);
        $id = $response->json('id');
        $genre = Genre::find($id);

        $response
            ->assertStatus(201)
            ->assertJson($genre->toArray());
        $this->assertTrue($response->json('is_active'));

        $response = $this->json('POST', route('genres.store'), [
            'name' => 'test',
            'is_active' => false
        ]);

        $response
            ->assertJsonFragment([
                'is_active' => false
            ]);
    }

    public function testUpdate()
    {
        $genre = factory(Genre::class)->create([
            'is_active' => false
        ]);
        $response = $this->json('PUT', route('genres.update', ['genre' => $genre->id]), [
            'name' => 'test',
            'is_active' => true
        ]);
        $id = $response->json('id');
        $genre = Genre::find($id);

        $response
            ->assertStatus(200)
            ->assertJson($genre->toArray())
            ->assertJsonFragment([
                'is_active' => true
            ]);

        $response = $this->json('PUT', route('genres.update', ['genre' => $genre->id]), [
            'name' => 'test1'
        ]);

        $response
            ->assertJsonFragment([
                'name' => 'test1'
            ]);
    }

    public function testDelete()
    {
        $genre = factory(Genre::class)->create();

        $response = $this->json('DELETE', route('genres.destroy', ['genre' => $genre->id]), []);

        $response
            ->assertStatus(204);
        $this->assertNull(Genre::find($genre->id));
        $this->assertNotNull(Genre::withTrashed()->find($genre->id));
    }
}
