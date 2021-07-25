<?php

namespace App\Http\Controllers;

use App\Models\Post;

use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::orderBy('creadted_at', 'desc')
            ->with('user_id, name, image')
            ->withCount('comments', 'likes')
            ->get();

        return response($posts, 200);
    }

    public function store(Request $request)
    {
        $attrs = $request->validate([
            'body' => 'required|string'
        ]);

        $posts = new Post();
        $posts->body = $attrs['body'];
        $posts->auth()->user()->id;
        $posts->save();

        $response = [
            'message' => 'Post Created',
            'post' => $posts
        ];

        return response($response, 200);
    }

    public function show($id)
    {
        $post = Post::where('id', $id)
            ->withCount('comments', 'likes')
            ->get();

        return response($post, 200);
    }

    public function update(Request $request, $id)
    {
        $posts = Post::find($id);

        if (!$posts) {
            return response([
                'message' => 'Post not found'
            ], 403);
        }

        if ($posts->user_id != auth()->user()->id) {
            return response(
                [
                    'message' => 'Permission denied'
                ]
            );
        }

        $attrs = $request->validate([
            'body' => 'required|string'
        ]);

        $posts->update([
            'body' => $attrs['body']
        ]);


        $response = [
            'message' => 'Post Updated',
            'post' => $posts
        ];

        return response($response, 200);
    }

    public function destroy($id)
    {
        $posts = Post::find($id);

        if (!$posts) {
            return response([
                'message' => 'Post not found'
            ], 403);
        }

        if ($posts->user_id != auth()->user()->id) {
            return response(
                [
                    'message' => 'Permission denied'
                ]
            );
        }

        $posts->comments()->delete();
        $posts->likes()->delete();
        $posts->delete();

        return response([
            'message' => 'Post Deleted'
        ], 200);
    }
}
