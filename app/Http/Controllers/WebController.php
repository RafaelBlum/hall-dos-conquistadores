<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use App\Models\Campaign;
use App\Models\Channel;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WebController extends Controller
{


    public function landing()
    {
        try {
            return view('landing');
        } catch (Exception $exception) {
            report($exception);
            return redirect()->back();
        }
    }

    public function landing2()
    {
        try {
            return view('landing2');
        } catch (Exception $exception) {
            report($exception);
            return redirect()->back();
        }
    }

    public function gameIntruderUsers()
    {
        try {
            $users = User::all(['id', 'name']);
            return view('game', compact('users'));
        } catch (Exception $exception) {
            report($exception);
            return redirect()->back();
        }
    }

    public function gameIntruderUsersV2()
    {
        try {
            $users = User::all(['id', 'name']);
            $authUser = auth()->user();
            return view('game2', compact('users', 'authUser'));
        } catch (Exception $exception) {
            report($exception);
            return redirect()->back();
        }
    }

    public function home()
    {
        try {
            $followedChannels = [];

            if (auth()->check()) {
                $followedChannels = auth()->user()->following()->withCount('followers')->get();
            }

            $suggestedChannels = Channel::withCount('followers')
                ->whereDoesntHave('followers', function ($query) {
                    $query->where('user_id', \auth()->id());
                })
                ->limit(5)
                ->get();

            $section = false;
            $channels = Channel::withCount('followers')->inRandomOrder()->limit(4)->get();
            $grid = $channels->count();

            $posts = Post::where('status', 'published')
                ->with(['author', 'author.channel', 'category'])
                ->withCount(['likes', 'allComments'])
                ->latest()
                ->limit(6)
                ->get();

            return view('home', compact(
                'posts',
                'section',
                'channels',
                'grid',
                'followedChannels',
                'suggestedChannels'
            ));
        } catch (Exception $exception) {
            report($exception);
            return redirect()->back();
        }
    }

    public function about()
    {
        $channels = Channel::all();
        $posts = Post::query()->where('status', '=', 'published')->get();
        $campaings = Campaign::all();

        return view('pages.about', compact('channels', 'posts', 'campaings'));
    }

    public function policy()
    {
        return view('pages.policy');
    }

    public function support()
    {
        return view('pages.support');
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route('app.home');
    }
}
