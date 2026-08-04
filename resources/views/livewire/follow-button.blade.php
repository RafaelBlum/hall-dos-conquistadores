<div class="flex items-center gap-4">
    <button
        wire:click="toggleFollow"
        @class([
            'px-8 py-3 rounded-lg font-semibold transition-all inline-flex items-center gap-2',
            'bg-white text-gray-900 hover:bg-gray-200' => !$isFollowing,
            'bg-gray-700 text-white hover:bg-red-600' => $isFollowing,
        ])
    >
        <svg wire:loading.remove wire:target="toggleFollow" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            @if($isFollowing)
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            @else
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            @endif
        </svg>
        <svg wire:loading wire:target="toggleFollow" class="animate-spin w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
        </svg>
        <span wire:loading.remove wire:target="toggleFollow">
            {{ $isFollowing ? 'Seguindo' : 'Seguir' }}
        </span>
        <span wire:loading wire:target="toggleFollow">
            Processando...
        </span>
    </button>
    <span class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-purple-600 dark:text-purple-400 bg-purple-100 dark:bg-purple-900/30 rounded-full">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
        {{ $followersCount }} {{ $followersCount === 1 ? 'seguidor' : 'seguidores' }}
    </span>
</div>
