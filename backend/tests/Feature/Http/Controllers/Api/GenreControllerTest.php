<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Controllers\Api\GenreController;
use App\Models\Category;
use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Http\Request;
use Mockery;
use Tests\Exception\TestException;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class GenreControllerTest extends TestCase
{

    use DatabaseMigrations, TestValidations, TestSaves;

    private $genre;
    private $sendData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->genre = factory(Genre::class)->create([
            'is_active' => true
        ]);
        $this->sendData = [
            'name' => 'name'
        ];
    }

    public function testIndex()
    {

        /** @var TestResponse $response */
        $response = $this->get(route('genres.index'));

        $response
            ->assertStatus(200)
            ->assertJson([$this->genre->toArray()]);
    }

    public function testShow()
    {

        /** @var TestResponse $response */
        $response = $this->get(route('genres.show', ['genre' => $this->genre->id]));

        $response
            ->assertStatus(200)
            ->assertJson($this->genre->toArray());
    }

    public function testInvalidationData()
    {

        $data = [
            'name' => '',
            'categories_id' => '',
        ];
        $this->AssertInValidationStoreAction($data, 'required');
        $this->AssertInValidationupdateAction($data, 'required');
    }

    public function testInvalidationCategoriesIdField()
    {
        $data = [
            'categories_id' => 'a'
        ];
        $this->AssertInValidationStoreAction($data, 'array');
        $this->AssertInValidationupdateAction($data, 'array');
        $data = [
            'categories_id' => [100]
        ];
        $this->AssertInValidationStoreAction($data, 'exists');
        $this->AssertInValidationupdateAction($data, 'exists');
    }

    public function testInvalidationMax()
    {
        $data = [
            'name' => str_repeat('a', 256)
        ];
        $this->AssertInValidationStoreAction($data, 'max.string', ['max' => 255]);
        $this->AssertInValidationupdateAction($data, 'max.string', ['max' => 255]);
    }

    public function testInvalidationIsActiveField()
    {
        $data = [
            'is_active' => 's'
        ];
        $this->AssertInValidationStoreAction($data, 'boolean');
        $this->AssertInValidationupdateAction($data, 'boolean');
    }

    public function testStoreAndUpdate()
    {
        $category =  factory(Category::class)->create();
        $data = [
            [
                'send_data' => $this->sendData + [
                    'categories_id' => [$category->id]
                ],
                'test_data' => $this->sendData + ['is_active' => true],
            ],
            [
                'send_data' => $this->sendData + [
                    'is_active' => true,
                    'categories_id' => [$category->id]
                ],
                'test_data' => $this->sendData + ['is_active' => true],
            ]
        ];

        foreach ($data as $key => $value) {
            $response = $this->assertStore(
                $value['send_data'],
                $value['test_data'] + ['deleted_at' => null]
            );
            $response->assertJsonStructure([
                'created_at', 'updated_at'
            ]);
            $this->assertUpdate(
                $value['send_data'],
                $value['test_data'] + ['deleted_at' => null]
            );
            $response->assertJsonStructure([
                'created_at', 'updated_at'
            ]);
        }
    }

    public function testRollbackStore()
    {
        $controller = Mockery::mock(GenreController::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $controller
            ->shouldReceive('validate')
            ->withAnyArgs()
            ->andReturn($this->sendData);

        $controller
            ->shouldReceive('rulesStore')
            ->withAnyArgs()
            ->andReturn([]);

        $controller->shouldReceive('handleRelations')
            ->once()
            ->andThrow(new TestException());

        $request = Mockery::mock(Request::class);

        $hasError = false;
        try {
            $controller->store($request);
        } catch (TestException $exception) {
            $this->assertCount(1, Genre::all());
            $hasError = true;
        }

        $this->assertTrue($hasError);
    }

    public function testDelete()
    {
        $response = $this->json('DELETE', route('genres.destroy', ['genre' => $this->genre->id]), []);

        $response
            ->assertStatus(204);
        $this->assertNull(Genre::find($this->genre->id));
        $this->assertNotNull(Genre::withTrashed()->find($this->genre->id));
    }

    protected function routeStore()
    {
        return route('genres.store');
    }

    protected function routeUpdate()
    {
        return route('genres.update', ['genre' => $this->genre->id]);
    }

    protected function model()
    {
        return Genre::class;
    }
}
