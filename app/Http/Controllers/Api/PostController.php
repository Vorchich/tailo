<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\PostResource;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::active()->latest()->with('media')->paginate(15);

        return response([
            'data' => [
                'posts' => PostResource::collection($posts),
                'result' => true,
            ]]);
    }

    public function show(Post $post)
    {
        if(!$post->active)
        {
            abort(404, 'Post not found!');
        }

        return response([
            'data' => [
                'posts' => PostResource::make($post),
                'result' => true,
            ]]);
    }
}
