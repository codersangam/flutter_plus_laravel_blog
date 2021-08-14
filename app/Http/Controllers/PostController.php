<?php

namespace App\Http\Controllers;

use App\Models\Post;

use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        return response([
            'posts' => Post::orderBy('created_at', 'desc')->with('user:id,name,image')->withCount('comments', 'likes')
                ->with('likes', function ($like) {
                    return $like->where('user_id', auth()->user()->id)
                        ->select('id', 'user_id', 'post_id')->get();
                })
                ->get()
        ], 200);
    }

    public function store(Request $request)
    {
        $attrs = $request->validate([
            'body' => 'required|string'
        ]);

        $image = $this->saveImage($request->image, 'posts');

        $posts = Post::create([
            'body' => $attrs['body'],
            'user_id' => auth()->user()->id,
            'image' => $image
        ]);

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
                ],
                403
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
