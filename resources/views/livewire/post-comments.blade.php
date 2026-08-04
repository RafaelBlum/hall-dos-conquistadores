<div class="mt-12 pt-8" x-data="{ deleteId: null }">
    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6">
        {{ $totalComments }} Comentários
    </h3>

    {{-- New Comment Form --}}
    @auth
        <div class="flex gap-4 mb-4" x-data="{ focused: false }">
            <img src="{{ auth()->user()->channel ? Storage::url(auth()->user()->channel->brand) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=7c3aed&color=fff' }}"
                 class="w-10 h-10 rounded-full object-cover flex-shrink-0">
            <div class="flex-1">
                <textarea wire:model="newComment" rows="1"
                    @focus="focused = true"
                    @blur="setTimeout(() => focused = false, 200)"
                    placeholder="Adicione um comentário..."
                    class="w-full px-0 py-2 bg-transparent border-0 border-b border-gray-300 dark:border-slate-600 text-gray-900 dark:text-white placeholder-gray-400 focus:border-gray-900 dark:focus:border-white focus:ring-0 outline-none resize-none transition-all text-sm"></textarea>
                @error('newComment') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                <div x-show="focused" x-transition class="flex justify-end gap-2 mt-2">
                    <button wire:click="$set('newComment', '')" class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-800 rounded-full transition-colors font-medium">
                        Cancelar
                    </button>
                    <button wire:click="addComment" class="inline-flex items-center gap-2 px-4 py-2 text-sm bg-gray-200 dark:bg-slate-700 text-gray-600 dark:text-gray-300 hover:bg-purple-600 hover:text-white dark:hover:bg-purple-600 dark:hover:text-white rounded-full transition-all font-medium">
                        <svg wire:loading wire:target="addComment" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span wire:loading.remove wire:target="addComment">Comentar</span>
                        <span wire:loading wire:target="addComment">Enviando...</span>
                    </button>
                </div>
            </div>
        </div>
    @else
        <div class="flex gap-4 mb-4 items-center">
            <div class="w-10 h-10 rounded-full bg-gray-200 dark:bg-slate-700 flex-shrink-0"></div>
            <a href="{{ route('login') }}" class="border-b border-gray-300 dark:border-slate-600 pb-2 flex-1 text-gray-400 text-sm hover:border-gray-400 transition-colors">
                Adicione um comentário...
            </a>
        </div>
    @endauth

    {{-- Delete Confirmation Modal --}}
    <div x-show="deleteId !== null" x-transition class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" style="display: none;">
        <div @click.away="deleteId = null" class="bg-white dark:bg-slate-800 rounded-2xl p-6 max-w-sm w-full mx-4 shadow-2xl">
            <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Remover comentário?</h4>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Esta ação não pode ser desfeita.</p>
            <div class="flex justify-end gap-3">
                <button @click="deleteId = null" class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-700 rounded-full transition-colors font-medium">
                    Cancelar
                </button>
                <button wire:click="deleteComment(deleteId)" @click="deleteId = null"
                    class="px-4 py-2 text-sm bg-red-500 hover:bg-red-600 text-white rounded-full transition-colors font-medium">
                    Remover
                </button>
            </div>
        </div>
    </div>

    {{-- Comments List --}}
    <div class="space-y-6">
        @forelse($comments as $comment)
            <div class="flex gap-3" x-data="{ menuOpen: false }">
                <img src="{{ $comment->user->channel ? Storage::url($comment->user->channel->brand) : 'https://ui-avatars.com/api/?name=' . urlencode($comment->user->name) . '&background=7c3aed&color=fff' }}"
                     class="w-10 h-10 rounded-full object-cover flex-shrink-0">
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $comment->user->name }}</span>
                                <span class="text-xs text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-sm text-gray-800 dark:text-gray-200 mt-1 leading-relaxed whitespace-pre-wrap">{{ $comment->body }}</p>
                            <div class="mt-2">
                                @auth
                                    <button wire:click="setReplyTo({{ $comment->id }})"
                                        class="text-xs font-semibold text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                                        Responder
                                    </button>
                                @endauth
                            </div>
                        </div>
                        @auth
                            @if(auth()->id() === $comment->user_id || $post->user_id === auth()->id())
                                <div class="relative flex-shrink-0">
                                    <button @click="menuOpen = !menuOpen" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors p-1 rounded-full hover:bg-gray-100 dark:hover:bg-slate-700">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/></svg>
                                    </button>
                                    <div x-show="menuOpen" @click.away="menuOpen = false" x-transition
                                        class="absolute right-0 top-full mt-1 bg-white dark:bg-slate-800 rounded-lg shadow-lg border border-gray-200 dark:border-slate-700 py-1 z-20 min-w-[160px]">
                                        <button @click="deleteId = {{ $comment->id }}; menuOpen = false"
                                            class="flex items-center gap-2 w-full px-4 py-2 text-sm text-red-500 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            Remover
                                        </button>
                                    </div>
                                </div>
                            @endif
                        @endauth
                    </div>

                    {{-- Reply Form --}}
                    @if($replyTo === $comment->id)
                        <div class="flex gap-3 mt-2">
                            <img src="{{ auth()->user()->channel ? Storage::url(auth()->user()->channel->brand) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=7c3aed&color=fff' }}"
                                 class="w-6 h-6 rounded-full object-cover flex-shrink-0">
                            <div class="flex-1">
                                <textarea wire:model="replyBody" rows="1"
                                    placeholder="Adicione uma resposta..."
                                    class="w-full px-0 py-1 bg-transparent border-0 border-b border-gray-300 dark:border-slate-600 text-gray-900 dark:text-white placeholder-gray-400 focus:border-gray-900 dark:focus:border-white focus:ring-0 outline-none resize-none transition-all text-sm"></textarea>
                                @error('replyBody') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                <div class="flex justify-end gap-2 mt-1">
                                    <button wire:click="cancelReply" class="px-3 py-1.5 text-xs text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-800 rounded-full transition-colors font-medium">
                                        Cancelar
                                    </button>
                                    <button wire:click="addReply" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs bg-gray-200 dark:bg-slate-700 text-gray-600 dark:text-gray-300 hover:bg-purple-600 hover:text-white dark:hover:bg-purple-600 dark:hover:text-white rounded-full transition-all font-medium">
                                        <svg wire:loading wire:target="addReply" class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        <span wire:loading.remove wire:target="addReply">Responder</span>
                                        <span wire:loading wire:target="addReply">Enviando...</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Replies --}}
                    @if($comment->replies->isNotEmpty())
                        <div class="mt-4 space-y-4">
                            @foreach($comment->replies as $reply)
                                <div class="flex gap-3" x-data="{ menuOpen: false }">
                                    <img src="{{ $reply->user->channel ? Storage::url($reply->user->channel->brand) : 'https://ui-avatars.com/api/?name=' . urlencode($reply->user->name) . '&background=7c3aed&color=fff' }}"
                                         class="w-6 h-6 rounded-full object-cover flex-shrink-0">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between">
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <span class="text-xs font-semibold text-gray-900 dark:text-white">{{ $reply->user->name }}</span>
                                                    <span class="text-xs text-gray-400">{{ $reply->created_at->diffForHumans() }}</span>
                                                </div>
                                                <p class="text-sm text-gray-800 dark:text-gray-200 mt-0.5">{{ $reply->body }}</p>
                                            </div>
                                            @auth
                                                @if(auth()->id() === $reply->user_id || $post->user_id === auth()->id())
                                                    <div class="relative flex-shrink-0">
                                                        <button @click="menuOpen = !menuOpen" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors p-1 rounded-full hover:bg-gray-100 dark:hover:bg-slate-700">
                                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/></svg>
                                                        </button>
                                                        <div x-show="menuOpen" @click.away="menuOpen = false" x-transition
                                                            class="absolute right-0 top-full mt-1 bg-white dark:bg-slate-800 rounded-lg shadow-lg border border-gray-200 dark:border-slate-700 py-1 z-20 min-w-[160px]">
                                                            <button @click="deleteId = {{ $reply->id }}; menuOpen = false"
                                                                class="flex items-center gap-2 w-full px-4 py-2 text-sm text-red-500 hover:bg-gray-100 dark:hover:bg-slate-700 transition-colors">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                                Remover
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endif
                                            @endauth
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <p class="text-gray-400 dark:text-gray-500 text-sm text-center py-8">Nenhum comentário ainda. Seja o primeiro!</p>
        @endforelse
    </div>

    @if($comments->hasPages())
        <div class="mt-6">
            {{ $comments->links() }}
        </div>
    @endif
</div>
