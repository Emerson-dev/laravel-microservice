<?php

namespace Tests\Feature\Http\Controllers\Api\VideoController;

use App\Models\Category;
use App\Models\Genre;
use App\Models\Video;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Traits\TestUploads;
use Tests\Traits\TestValidations;

class VideoContollerUploadsTest extends BaseVideoControllerTestCase
{
    use TestValidations, TestUploads;

    public function testInvalidationThumbField()
    {
        $this->assertInvalidationFile(
            'thumb_file',
            'jpg',
            Video::THUMB_FILE_MAX_SIZE,
            'image'
        );
    }

    public function testInvalidationBannerField()
    {
        $this->assertInvalidationFile(
            'banner_file',
            'jpg',
            Video::BANNER_FILE_MAX_SIZE,
            'image'
        );
    }

    public function testInvalidationTrailerField()
    {
        $this->assertInvalidationFile(
            'trailer_file',
            'mp4',
            Video::TRAILER_FILE_MAX_SIZE,
            'mimetypes',
            ['values' => 'video/mp4']
        );
    }

    public function testInvalidationVideoFileField()
    {
        $this->assertInvalidationFile(
            'video_file',
            'mp4',
            Video::VIDEO_FILE_MAX_SIZE,
            'mimetypes',
            ['values' => 'video/mp4']
        );
    }

    public function testStoreWithFiles()
    {
        UploadedFile::fake()->image("image.jpg");
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
        $id = $response->json('data.id');
        foreach ($files as $file) {
            Storage::assertExists("$id/{$file->hashName()}");
        }
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
        $id = $response->json('data.id');
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
        $id = $response->json('data.id');
        foreach ($files as $file) {
            // Storage::assertExists("$id/{$file->hashName()}");
        }
    }

    protected function getFiles()
    {
        return [
            'video_file' => UploadedFile::fake()->create("video_file.mp4"),
            'thumb_file' => UploadedFile::fake()->image("thumb_file.jpg"),
            'trailer_file' => UploadedFile::fake()->create("trailer_file.mp4"),
            'banner_file' => UploadedFile::fake()->image("banner_file.jpg")
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
