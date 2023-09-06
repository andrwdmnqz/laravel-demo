<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

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
        //dd($request->all());

        $inputFields = $request->validate([
            'title' => 'required',
            'body' => 'required',
            'img' => 'image'
        ]);

        $inputFields['title'] = strip_tags($inputFields['title']);
        $inputFields['body'] = strip_tags($inputFields['body']);

        if($request->hasFile('img')) {
            if($post->image != 'img/default-image.png') {
                $filepathToDelete = storage_path('app/public/' . $post->image);
                File::delete($filepathToDelete);
            }

            $uploadedFile = $request->file('img');
            $filename = $uploadedFile->store('public/img');
            $filename = str_replace('public/', '', $filename);
            $inputFields['image'] = $filename;
        }

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
            'body' => 'required',
            'img' => 'image'
        ]);

        $filename = 'img/default-image.png';

        if ($request->hasFile('img')) {
            $uploadedFile = $request->file('img');
            $filename = $uploadedFile->store('public/img');

            $filename = str_replace('public/', '', $filename);
        }

        $inputFields['title'] = strip_tags($inputFields['title']);
        $inputFields['body'] = strip_tags($inputFields['body']);

        $inputFields['user_id'] = auth()->id();
        $inputFields['image'] = $filename;

        Post::create($inputFields);

        return response()->json([
            'status' => 201
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
                    'image' => $post->image,
                    'title' => $post->title,
                    'author' => $post->getAuthor->name,
                    'body' => $post->body,
                    'is_author' => $is_author,
                    'author_id' => $post->getAuthor->id,
                    'last_seen' => $post->getAuthor->last_seen,
                    'is_online' => (Cache::has('is_online' . $post->getAuthor->id))
                ];
            }
        }
        return response()->json($output);
    }
}
