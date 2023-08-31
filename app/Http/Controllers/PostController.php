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

        return response()->json([
            'text' => 'text'
        ]);
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

        return response()->json([
            'status' => 200
        ]);
    }

    public function showPosts() {
        $posts = Post::all();
        $output = [];

        if ($posts->count() > 0) {
            foreach($posts as $post) {
                if(auth()->user()) {
                    $is_author = $post->user_id === auth()->user()->id;

                }
                else {
                    $is_author = false;
                }

                $output[] = [
                    'id' => $post->id,
                    'title' => $post->title,
                    'author' => $post->getAuthor->name,
                    'body' => $post->body,
                    'is_author' => $is_author
                ];
            }
        }
        return response()->json($output);
    }
}
