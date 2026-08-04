<?php

namespace App\Livewire;

use App\Models\Comment;
use App\Models\Post;
use Livewire\Component;
use Livewire\WithPagination;

class PostComments extends Component
{
    use WithPagination;

    public Post $post;
    public $newComment = '';
    public $replyTo = null;
    public $replyBody = '';

    public function mount(Post $post)
    {
        $this->post = $post;
    }

    public function addComment()
    {
        if (auth()->guest()) {
            return redirect()->route('login');
        }

        $this->validate(['newComment' => 'required|string|min:2']);

        Comment::create([
            'user_id' => auth()->id(),
            'post_id' => $this->post->id,
            'body' => $this->newComment,
        ]);

        $this->newComment = '';
        $this->resetPage();
    }

    public function setReplyTo($commentId)
    {
        if (auth()->guest()) {
            return redirect()->route('login');
        }
        $this->replyTo = $commentId;
        $this->replyBody = '';
    }

    public function cancelReply()
    {
        $this->replyTo = null;
        $this->replyBody = '';
    }

    public function addReply()
    {
        if (auth()->guest()) {
            return redirect()->route('login');
        }

        $this->validate(['replyBody' => 'required|string|min:2']);

        Comment::create([
            'user_id' => auth()->id(),
            'post_id' => $this->post->id,
            'parent_id' => $this->replyTo,
            'body' => $this->replyBody,
        ]);

        $this->replyTo = null;
        $this->replyBody = '';
    }

    public function deleteComment($commentId)
    {
        $comment = Comment::findOrFail($commentId);
        if (auth()->id() === $comment->user_id || $this->post->user_id === auth()->id()) {
            $comment->delete();
        }
    }

    public function render()
    {
        $totalComments = Comment::where('post_id', $this->post->id)->count();

        $comments = Comment::where('post_id', $this->post->id)
            ->whereNull('parent_id')
            ->with(['user', 'user.channel', 'replies.user', 'replies.user.channel'])
            ->latest()
            ->paginate(10);

        return view('livewire.post-comments', compact('comments', 'totalComments'));
    }
}
