<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Like;
use App\Models\Post;

class LikeController extends Controller
{
    public function likeOrDislike($id)
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

        $like = $posts->likes()->where('user_id', auth()->user()->id)->first();

        // if not liked then like
        if (!$like) {
            Like::create([
                'post_id' => $id,
                'user_id' => auth()->user()->id
            ]);

            return response(
                [
                    'message' => 'Liked'
                ],
                200
            );
        }

        // else dislike it
        $like->delete();
        return response([
            'message' => 'Disliked'
        ], 200);
    }
}
