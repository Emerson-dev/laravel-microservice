<?php

namespace Tests\Feature\Models;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testList()
    {

        factory(Category::class, 1)->create();
        $categories = Category::all();
        $this->assertCount(1, $categories);
        $categoryKeys = array_keys($categories->first()->getAttributes());
        $this->assertEqualsCanonicalizing(
            [
                'id',
                'name',
                'description',
                'is_active',
                'deleted_at',
                'created_at',
                'updated_at'
            ],
            $categoryKeys
        );

        // $response = $this->get('/');

        // $response->assertStatus(200);
    }

    public function testCreate()
    {
        $category = Category::create([
            'name' => 'Teste'
        ]);
        $category->refresh();

        $this->assertEquals('Teste', $category->name);
        $this->assertNull($category->description);
        $this->assertTrue($category->is_active);

        $category = Category::create([
            'name' => 'Teste',
            'description' => null
        ]);

        $this->assertNull($category->description);

        $category = Category::create([
            'name' => 'Teste',
            'description' => 'Teste description'
        ]);

        $this->assertEquals('Teste description', $category->description);

        $category = Category::create([
            'name' => 'Teste',
            'is_active' => false
        ]);

        $this->assertFalse($category->is_active);

        $category = Category::create([
            'name' => 'Teste',
            'is_active' => true
        ]);

        $this->assertTrue($category->is_active);
    }

    public function testUpdate()
    {
        /**
         *  @var Category $category
         */
        $category = factory(Category::class)->create([
            'description' => 'Teste description',
            'is_active' => false
        ])->first();

        $data = [
            'name' => 'Teste update',
            'description' => 'Teste description update',
            'is_active' => true
        ];

        $category->update($data);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $category->{$key});
        }
    }
}
