<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PostController;
use GuzzleHttp\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Sanctum;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


// Public Routes

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);


// Protected Routes
Route::group(['middleware' => ['auth:sanctum']], function () {

    // User
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('user', [AuthController::class, 'user']);
    Route::post('user', [AuthController::class, 'update']);

    // Posts
    Route::get('posts', [PostController::class, 'index']);  //all posts
    Route::post('posts', [PostController::class, 'store']);  //create post
    Route::get('posts/{id}', [PostController::class, 'show']);  //view single post
    Route::put('posts/{id}', [PostController::class, 'update']);  //update post
    Route::delete('posts/{id}', [PostController::class, 'destroy']);  //delete post

    // Comments
    Route::get('posts/{id}/comments', [CommentController::class, 'index']);  //all comments
    Route::post('posts/{id}/comments', [CommentController::class, 'store']);  //create comments
    Route::put('comments/{id}', [CommentController::class, 'update']);  //update comments
    Route::delete('comments/{id}', [CommentController::class, 'destroy']);  //delete comments

    // Likes
    Route::post('posts/{id}/likes', [LikeController::class, 'likeOrDislike']);  //likes or dislikes
});
