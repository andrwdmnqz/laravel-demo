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
}
