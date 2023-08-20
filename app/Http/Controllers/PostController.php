<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{

    public function deletePost(Post $post) {
        if (auth()->user()->id === $post->user_id) {
            $post->delete();
        }

        return redirect('/');
    }
    public function updatePost(Request $request, Post $post) {
        if (auth()->user()->id !== $post->user_id) {
            return redirect('/');
        }

        $inputFields = $request->validate([
            'title' => 'required',
            'body' => 'required'
        ]);

        $inputFields['title'] = strip_tags($inputFields['title']);
        $inputFields['body'] = strip_tags($inputFields['body']);

        $post->update($inputFields);

        return redirect('/');
    }
    public function showEditView(Post $post) {
        if (auth()->user()->id !== $post->user_id) {
            return redirect('/');
        }

        return view('edit-post', ['post' => $post]);
    }

    public function createPost(Request $request) {
        $inputFields = $request->validate([
            'title' => 'required',
            'body' => 'required'
        ]);

        $inputFields['title'] = strip_tags($inputFields['title']);
        $inputFields['body'] = strip_tags($inputFields['body']);

        $inputFields['user_id'] = auth()->id();

        Post::create($inputFields);

        return redirect('/');
    }
}
