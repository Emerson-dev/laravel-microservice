<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\CategoryResource;
use App\Models\Category;

class CategoryController extends BasicCrudController
{
    private $rules;

    public function __construct()
    {
        $this->rules = [
            'name' => 'required|max:255',
            'description' => 'nullable',
            'is_active' => 'boolean'
        ];
    }

    // public function index()
    // {
    //     $collection = parent::index();
    //     return CategoryResource::collection($collection);
    //     // return new CategoryCollection($collection);
    // }

    // public function show($id)
    // {
    //     $obj = parent::show($id);
    //     return new CategoryResource($obj);
    // }

    protected function model()
    {
        return Category::class;
    }

    protected function rulesStore()
    {
        return $this->rules;
    }

    protected function rulesUpdate()
    {
        return $this->rules;
    }


    protected function resource()
    {
        return CategoryResource::class;
    }

    protected function resourceCollection()
    {
        return $this->resource();
    }
}
