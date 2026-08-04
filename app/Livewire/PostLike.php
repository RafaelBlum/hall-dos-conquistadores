<?php

namespace App\Livewire;

use App\Models\Post;
use Livewire\Component;

class PostLike extends Component
{
    public Post $post;
    public $isLiked = false;
    public $likesCount = 0;
    public $showAnimation = false;

    public function mount(Post $post)
    {
        $this->post = $post;
        $this->likesCount = $post->likes()->count();
        $this->checkLiked();
    }

    public function checkLiked()
    {
        $this->isLiked = auth()->check()
            ? $this->post->likers()->where('user_id', auth()->id())->exists()
            : false;
    }

    public function toggleLike()
    {
        if (auth()->guest()) {
            return redirect()->route('login');
        }

        $this->post->likers()->toggle(auth()->id());
        $this->checkLiked();
        $this->likesCount = $this->post->likes()->count();

        if ($this->isLiked) {
            $this->showAnimation = true;
            $this->dispatch('like-animation');
        }
    }

    public function render()
    {
        return view('livewire.post-like');
    }
}
