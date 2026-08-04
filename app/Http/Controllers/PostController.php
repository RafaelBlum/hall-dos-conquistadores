<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use MongoDB\Driver\Query;

class PostController extends Controller
{
    public function index()
    {
        try {

            $posts = Post::where('status', '=', 'PUBLISHED')
                ->orWhere('scheduled_for', '<', now())
                ->with(['author', 'author.channel', 'category'])
                ->withCount(['likes', 'allComments'])
                ->paginate(6)
                ->fragment('posts');

            $categories = Category::whereHas('posts', function ($query) {
                $query->where('status', '=', 'PUBLISHED')->orWhere('scheduled_for', '<', now());
            })->get();

            return view('pages.index', compact('posts', 'categories'));
        } catch (Exception $exception) {
            if (env('APP_DEBUG')) {
                return redirect()->back();
            }

            return redirect()->back();
        }
    }

    public function post($slug)
    {
        try {
            $post = Post::where('slug', $slug)->firstOrFail();

            $viewKey = 'post_viewed_' . $post->id;
            if (!session()->has($viewKey)) {
                $post->increment('views');
                session()->put($viewKey, true);
            }

            return view('pages.post', compact('post'));
        } catch (Exception $exception) {
            if (env('APP_DEBUG')) {
                return redirect()->back();
            }
            return redirect()->back();
        }
    }
}
