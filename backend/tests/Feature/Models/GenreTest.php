<?php

namespace Tests\Feature\Models;

use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Ramsey\Uuid\Uuid as RamseyUuid;

class GenreTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testList()
    {

        factory(Genre::class, 1)->create();
        $categories = Genre::all();
        $this->assertCount(1, $categories);
        $genreKeys = array_keys($categories->first()->getAttributes());
        $this->assertEqualsCanonicalizing(
            [
                'id',
                'name',
                'is_active',
                'deleted_at',
                'created_at',
                'updated_at'
            ],
            $genreKeys
        );
    }

    public function testCreate()
    {
        $genre = Genre::create([
            'name' => 'Teste'
        ]);
        $genre->refresh();

        $this->assertEquals('Teste', $genre->name);
        $this->assertTrue($genre->is_active);

        $genre = Genre::create([
            'name' => 'Teste',
            'is_active' => false
        ]);

        $this->assertFalse($genre->is_active);

        $genre = Genre::create([
            'name' => 'Teste',
            'is_active' => true
        ]);

        $this->assertTrue($genre->is_active);

        $this->assertNotEmpty($genre->id);
        $this->assertIsString($genre->id);

        $this->assertTrue(RamseyUuid::isValid($genre->id));
    }

    public function testUpdate()
    {
        /**
         *  @var Genre $genre
         */
        $genre = factory(Genre::class)->create([
            'name' => 'Teste',
            'is_active' => false
        ])->first();

        $data = [
            'name' => 'Teste update',
            'is_active' => true
        ];

        $genre->update($data);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $genre->{$key});
        }
    }

    public function testDelete()
    {
        /**
         *  @var Genre $genre
         */
        $genre = factory(Genre::class)->create([
            'name' => 'Teste',
            'is_active' => false
        ])->first();

        $genre->delete();

        $this->assertSoftDeleted($genre);
    }
}
