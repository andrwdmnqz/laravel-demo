<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function adminView() {
        return view('admin-view');
    }

    public function posts() {
        $posts = Post::all();
        $content = [];

        if ($posts->count() > 0) {
            foreach ($posts as $post) {
                $content[] = [
                    'id' => $post->id,
                    'title' => $post->title,
                    'created_at' => $post->created_at,
                    'updated_at' => $post->updated_at,
                    'author' => $post->getAuthor->name
                ];
            }
        }
        return response()->json($content);
    }

    public function editInfo(Post $post) {
        return response()->json($post);
    }

    public function updatePost(Request $request, Post $post) {
        $inputFields = $request->validate([
            'title' => 'required',
            'body' => 'required'
        ]);

        $inputFields['title'] = strip_tags($inputFields['title']);
        $inputFields['body'] = strip_tags($inputFields['body']);

        $post->update($inputFields);
        return response()->json([
            'text' => 'text'
        ]);
    }

    public function deletePost(Post $post) {
        $post->delete();

        return response()->json([
            'text' => 'text'
        ]);
    }
}
