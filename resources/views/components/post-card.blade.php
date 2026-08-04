@props(['post'])

<article class="group bg-gray-50 dark:bg-slate-800 rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
    <a href="{{ route('posts.post', ['slug' => $post->slug]) }}" class="block overflow-hidden">
        <img src="{{ Storage::url($post->featured_image_url) }}" alt="{{ $post->title }}"
            class="w-full h-44 object-cover group-hover:scale-105 transition-transform duration-500">
    </a>
    <div class="p-5">
        <div class="flex items-center justify-between mb-2">
            <a href="{{ route('posts.category', ['slug' => $post->category->slug]) }}"
                class="inline-block px-2.5 py-0.5 text-[10px] font-semibold uppercase rounded-full bg-purple-100 text-purple-700 dark:bg-purple-900/50 dark:text-purple-300 hover:bg-purple-200 dark:hover:bg-purple-800/50 transition-colors">
                {{ $post->category->title }}
            </a>
            <a href="{{ route('my.channel', ['slug' => $post->author->channel->slug]) }}"
                class="flex items-center gap-1.5 group/author">
                <span class="text-xs text-gray-500 dark:text-gray-400 group-hover/author:text-purple-600 transition-colors truncate max-w-[100px]">
                    {{ $post->author->channel->title }}
                </span>
                <img src="{{ Storage::url($post->author->channel->brand) }}"
                    class="w-6 h-6 rounded-full object-cover ring-1 ring-gray-200 dark:ring-slate-600">
            </a>
        </div>
        <a href="{{ route('posts.post', ['slug' => $post->slug]) }}">
            <h3 class="text-base font-bold text-gray-900 dark:text-white mb-1.5 group-hover:text-purple-600 transition-colors line-clamp-2">
                {{ Str::limit($post->title, 50) }}
            </h3>
        </a>
        <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2 mb-3">{!! Str::limit(strip_tags($post->summary), 100) !!}</p>

        <div class="flex items-center gap-3 text-[11px] text-gray-400 dark:text-gray-500">
            <span class="flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                {{ $post->views }}
            </span>
            <span class="flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
                {{ $post->likes_count ?? $post->likes->count() }}
            </span>
            <span class="flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                {{ $post->all_comments_count ?? $post->allComments->count() }}
            </span>
        </div>
    </div>
</article>
