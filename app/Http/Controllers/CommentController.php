<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Comment;

class CommentController extends Controller
{
    //Get all comments of a post
    public function index($id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response([
                'message' => 'Post not found'
            ], 403);
        }

        return response([
            'comments' => $post->comments()->with('user:id,name,image')->get()
        ], 200);
    }

    //Create comment
    public function store(Request $request, $id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response([
                'message' => 'Post not found'
            ], 403);
        }

        $attrs = $request->validate([
            'comment' => 'required|string'
        ]);

        Comment::create([
            'comment' => $attrs['comment'],
            'post_id' => $id,
            'user_id' => auth()->user()->id
        ]);

        return response([
            'message' => 'Comment Created'
        ], 200);
    }

    // Update Comment
    public function update(Request $request, $id)
    {
        $comment = Comment::find($id);

        if (!$comment) {
            return response([
                'message' => 'Comment not found'
            ], 403);
        }

        if ($comment->user_id != auth()->user()->id) {
            return response(
                [
                    'message' => 'Permission denied'
                ],
                403
            );
        }

        $attrs = $request->validate([
            'comment' => 'required|string'
        ]);

        $comment->update([
            'comment' => $attrs['comment'],
        ]);

        return response([
            'message' => 'Comment updated'
        ], 200);
    }

    //Delete comment
    public function destroy($id)
    {
        $comment = Comment::find($id);

        if (!$comment) {
            return response([
                'message' => 'Comment not found'
            ], 403);
        }

        if ($comment->user_id != auth()->user()->id) {
            return response(
                [
                    'message' => 'Permission denied'
                ],
                403
            );
        }

        $comment->delete();
        return response([
            'message' => 'Comment Deleted'
        ], 200);
    }
}
