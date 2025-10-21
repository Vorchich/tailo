<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with('sizes')->get();

        return response([
            'data' => [
                'categories' => CategoryResource::collection($categories),
                'result' => true
            ]]);
    }

    public function show(Category $category)
    {
        $category->loadMissing('sizes');

        return response([
            'data' => [
                'category' => CategoryResource::make($category),
                'result' => true,
                ]]);
    }

    public function create()
    {

    }

    public function edit()
    {

    }


}
