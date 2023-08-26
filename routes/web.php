<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Models\Post;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    $posts = Post::all();
    return view('home', ['posts' => $posts]);
});

//User routes
Route::post('/register', [UserController::class, 'createUser']);
Route::post('/logout', [UserController::class, 'logout'])->name('logout');
Route::post('/login', [UserController::class, 'login']);

//Post routes
Route::post('/create-post', [PostController::class, 'createPost'])->name('create_post');
Route::get('/show-posts', [PostController::class, 'showPosts'])->name('show_posts');
Route::get('/edit-post/{post}', [PostController::class, 'showEditView']);
Route::put('/edit-post/{post}', [PostController::class, 'updatePost']);
Route::delete('/delete-post/{post}', [PostController::class, 'deletePost']);
