<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Controllers\Api\VideoController;
use App\Models\Category;
use App\Models\Genre;
use Tests\TestCase;
use App\Models\Video;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\Exception\TestException;
use Tests\Traits\TestSaves;
use Tests\Traits\TestUploads;
use Tests\Traits\TestValidations;

class VideoControllerTest extends TestCase
{

    use DatabaseMigrations, TestValidations, TestSaves, TestUploads;

    private $video;
    private $sendData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->video = factory(Video::class)->create([
            'opened' => false
        ]);
        $this->sendData = [
            'title' => 'title',
            'description' => 'description',
            'year_launched' => 2010,
            'rating' => Video::RATING_LIST[0],
            'duration' => 90,
        ];
    }

    public function testIndex()
    {

        /** @var TestResponse $response */
        $response = $this->get(route('videos.index'));

        $response
            ->assertStatus(200)
            ->assertJson([$this->video->toArray()]);
    }

    public function testInvalidationData()
    {
        $data = [
            'title' => '',
            'description' => '',
            'year_launched' => '',
            'rating' => '',
            'duration' => '',
            'categories_id' => '',
            'genres_id' => '',
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

    public function testInvalidationGenresIdField()
    {
        $data = [
            'genres_id' => 'a'
        ];
        $this->AssertInValidationStoreAction($data, 'array');
        $this->AssertInValidationupdateAction($data, 'array');
        $data = [
            'genres_id' => [100]
        ];
        $this->AssertInValidationStoreAction($data, 'exists');
        $this->AssertInValidationupdateAction($data, 'exists');
    }

    public function testInvalidationMax()
    {
        $data = [
            'title' => str_repeat('a', 256)
        ];
        $this->AssertInValidationStoreAction($data, 'max.string', ['max' => 255]);
        $this->AssertInValidationupdateAction($data, 'max.string', ['max' => 255]);
    }

    public function testInvalidationInteger()
    {
        $data = [
            'duration' => 's'
        ];
        $this->AssertInValidationStoreAction($data, 'integer');
        $this->AssertInValidationupdateAction($data, 'integer');
    }

    public function testInvalidationYearLaunchedField()
    {
        $data = [
            'opened' => 's'
        ];
        $this->AssertInValidationStoreAction($data, 'boolean');
        $this->AssertInValidationupdateAction($data, 'boolean');
    }

    public function testInvalidationOpenedField()
    {
        $data = [
            'year_launched' => 's'
        ];
        $this->AssertInValidationStoreAction($data, 'date_format', ['format' => 'Y']);
        $this->AssertInValidationupdateAction($data, 'date_format', ['format' => 'Y']);
    }

    public function testInvalidationVideoFileField()
    {
        $this->assertInvalidationFile(
            'video_file',
            'mp4',
            20,
            'mimetypes',
            ['values' => 'video/mp4']
        );
    }

    public function testSaveWitoutFiles()
    {
        $category = factory(Category::class)->create();
        $genre = factory(Genre::class)->create();
        $genre->categories()->sync($category->id);

        $data = [
            [
                'send_data' => $this->sendData + [
                    'categories_id' => [$category->id],
                    'genres_id' => [$genre->id]
                ],
                'test_data' => $this->sendData + ['opened' => false],
            ],
            [
                'send_data' => $this->sendData + [
                    'opened' => true,
                    'categories_id' => [$category->id],
                    'genres_id' => [$genre->id]
                ],
                'test_data' => $this->sendData + ['opened' => true],
            ],
            [
                'send_data' => $this->sendData + [
                    'rating' => Video::RATING_LIST[1],
                    'categories_id' => [$category->id],
                    'genres_id' => [$genre->id]
                ],
                'test_data' => $this->sendData + ['rating' => Video::RATING_LIST[1]],
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
            $this->assertHasCategory(
                $response->json('id'),
                $value['send_data']['categories_id'][0]
            );
            $this->assertHasGenre(
                $response->json('id'),
                $value['send_data']['genres_id'][0]
            );
            $this->assertUpdate(
                $value['send_data'],
                $value['test_data'] + ['deleted_at' => null]
            );
            $response->assertJsonStructure([
                'created_at', 'updated_at'
            ]);
            $this->assertHasCategory(
                $response->json('id'),
                $value['send_data']['categories_id'][0]
            );
            $this->assertHasGenre(
                $response->json('id'),
                $value['send_data']['genres_id'][0]
            );
        }
    }

    public function assertHasCategory($videoId, $categoryId)
    {
        $this->assertDatabaseHas('category_video', [
            'video_id' => $videoId,
            'category_id' => $categoryId,
        ]);
    }

    protected function assertHasGenre($videoId, $genreId)
    {
        $this->assertDatabaseHas('genre_video', [
            'video_id' => $videoId,
            'genre_id' => $genreId,
        ]);
    }

    public function testInvalidationRatingField()
    {
        $data = [
            'rating' => 0
        ];
        $this->AssertInValidationStoreAction($data, 'in');
        $this->AssertInValidationupdateAction($data, 'in');
    }

    public function testShow()
    {

        /** @var TestResponse $response */
        $response = $this->get(route('videos.show', ['video' => $this->video->id]));

        $response
            ->assertStatus(200)
            ->assertJson($this->video->toArray());
    }

    public function testStoreWithoutFiles()
    {
        Storage::fake();
        $files = $this->getFiles();

        $category =  factory(Category::class)->create();
        $genre =  factory(Genre::class)->create();
        $genre->categories()->sync($category->id);

        $response = $this->json(
            'POST',
            $this->routeStore(),
            $this->sendData +
                [
                    'categories_id' => [$category->id],
                    'genres_id' => [$genre->id]
                ] +
                $files
        );

        $response->assertStatus(201);
        $id = $response->json('id');
        foreach ($files as $file) {
            Storage::assertExists("$id/{$file->hashName()}");
        }
    }
    public function testUpdateWithoutFiles()
    {
        Storage::fake();
        $files = $this->getFiles();

        $category =  factory(Category::class)->create();
        $genre =  factory(Genre::class)->create();
        $genre->categories()->sync($category->id);

        $response = $this->json(
            'PUT',
            $this->routeUpdate(),
            $this->sendData +
                [
                    'categories_id' => [$category->id],
                    'genres_id' => [$genre->id]
                ] +
                $files
        );

        $response->assertStatus(200);
        $id = $response->json('id');
        foreach ($files as $file) {
            // Storage::assertExists("$id/{$file->hashName()}");
        }
    }

    public function testDelete()
    {
        $response = $this->json('DELETE', route('videos.destroy', ['video' => $this->video->id]), []);

        $response
            ->assertStatus(204);
        $this->assertNull(Video::find($this->video->id));
        $this->assertNotNull(Video::withTrashed()->find($this->video->id));
    }

    protected function getFiles()
    {
        return [
            'video_file' => UploadedFile::fake()->create("video_file.mp4")
        ];
    }

    protected function routeStore()
    {
        return route('videos.store');
    }

    protected function routeUpdate()
    {
        return route('videos.update', ['video' => $this->video->id]);
    }

    protected function model()
    {
        return Video::class;
    }
}
