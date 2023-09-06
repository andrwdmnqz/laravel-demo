<?php

use App\Http\Controllers\HomeController;
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

// User routes
Route::post('/register', [UserController::class, 'createUser'])->name('register');
Route::post('/logout', [UserController::class, 'logout'])->name('logout');
Route::post('/login', [UserController::class, 'login'])->name('login');
Route::get('/status', [UserController::class, 'showStatus'])->name('user_status');
Route::get('/user-cab/{user}', [UserController::class, 'getUser'])->name('user_info');
Route::put('/edit-user/{user}', [UserController::class, 'updateUser'])->name('update_user');

// Post routes
Route::post('/create-post', [PostController::class, 'createPost'])->name('create_post');
Route::get('/show-posts', [PostController::class, 'showPosts'])->name('show_posts');
Route::get('/edit-post/{post}', [PostController::class, 'showEditView']);
Route::put('/edit-post/{post}', [PostController::class, 'updatePost']);
Route::delete('/delete-post/{post}', [PostController::class, 'deletePost']);

// Admin routes
Route::middleware(['auth', 'admin'])->group(function() {
    Route::get('/admin-view', [HomeController::class, 'adminView'])->name('admin_view');
    Route::post('/admin-view/show-posts', [HomeController::class, 'posts'])->name('admin_posts');
    Route::get('/admin-view/edit-post/{post}', [HomeController::class, 'editInfo']);
    Route::put('/admin-view/edit-post/{post}', [HomeController::class, 'updatePost']);
    Route::delete('/admin-view/delete-post/{post}', [HomeController::class, 'deletePost']);
});
