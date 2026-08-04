<?php

namespace App\Livewire;

use App\Models\Channel;
use Livewire\Component;

class FollowButton extends Component
{
    public $channelId;
    public $channelName;
    public $isFollowing = false;
    public $followersCount = 0;

    public function mount(Channel $channel)
    {
        $this->channelId = $channel->id;
        $this->channelName = $channel->name;
        $this->followersCount = $channel->followers()->count();
        $this->checkFollowing();
    }

    public function checkFollowing()
    {
        $this->isFollowing = auth()->check()
            ? auth()->user()->following()->where('channel_id', $this->channelId)->exists()
            : false;
    }

    public function toggleFollow()
    {
        if (auth()->guest()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if ($user->channel && $user->channel->id === $this->channelId) {
            return;
        }

        auth()->user()->following()->toggle($this->channelId);
        $this->checkFollowing();
        $this->followersCount = Channel::find($this->channelId)->followers()->count();
    }

    public function render()
    {
        return view('livewire.follow-button');
    }
}
