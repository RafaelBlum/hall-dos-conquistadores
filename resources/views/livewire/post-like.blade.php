<div class="flex items-center gap-4">
    <button wire:click="toggleLike"
        class="group inline-flex items-center gap-2 px-5 py-2.5 rounded-full transition-all duration-300
        {{ $isLiked
            ? 'bg-blue-500/10 text-blue-500 border border-blue-500/30'
            : 'bg-gray-100 dark:bg-slate-800 text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-slate-700 hover:bg-blue-50 dark:hover:bg-blue-500/10 hover:text-blue-500 hover:border-blue-300 dark:hover:border-blue-500/30' }}">

        <span class="relative flex items-center justify-center">
            <svg wire:loading.remove wire:target="toggleLike"
                class="w-5 h-5 transition-all duration-300 {{ $isLiked ? 'scale-110' : 'group-hover:scale-110' }}"
                fill="{{ $isLiked ? 'currentColor' : 'none' }}"
                stroke="currentColor"
                viewBox="0 0 24 24"
                stroke-width="{{ $isLiked ? '0' : '2' }}">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M6.633 10.5c.806 0 1.533-.446 2.031-1.08a9.041 9.041 0 012.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 00.322-1.672V3a.75.75 0 01.75-.75A2.25 2.25 0 0116.5 4.5c0 1.152-.26 2.243-.723 3.218-.266.558.107 1.282.725 1.282h3.126c1.026 0 1.945.694 2.054 1.715.045.422.068.85.068 1.285a11.95 11.95 0 01-2.649 7.521c-.388.482-.987.729-1.605.729H13.48c-.483 0-.964-.078-1.423-.23l-3.114-1.04a4.501 4.501 0 00-1.423-.23H5.904M14.25 9h2.25M5.904 18.75c.083.205.173.405.27.602.197.4-.078.898-.523.898h-.908c-.889 0-1.713-.518-1.972-1.368a12 12 0 01-.521-3.507c0-1.553.295-3.036.831-4.398C3.387 10.203 4.167 9.75 5 9.75h1.053c.472 0 .745.556.5.96a8.958 8.958 0 00-1.302 4.665c0 1.194.232 2.333.654 3.375z"/>
            </svg>

            @if($isLiked)
                <span class="absolute inset-0 flex items-center justify-center pointer-events-none">
                    <span class="like-burst-1 absolute w-1.5 h-1.5 bg-blue-400 rounded-full opacity-0"></span>
                    <span class="like-burst-2 absolute w-1 h-1 bg-blue-300 rounded-full opacity-0"></span>
                    <span class="like-burst-3 absolute w-1.5 h-1.5 bg-sky-400 rounded-full opacity-0"></span>
                    <span class="like-burst-4 absolute w-1 h-1 bg-blue-500 rounded-full opacity-0"></span>
                </span>
            @endif
        </span>

        <span class="font-semibold text-sm select-none">
            {{ $isLiked ? 'Curtido' : 'Curtir' }}
        </span>
    </button>

    <span class="text-sm text-gray-500 dark:text-gray-400 font-medium">
        {{ $likesCount }} {{ $likesCount === 1 ? 'curtida' : 'curtidas' }}
    </span>

    <style>
        .like-burst-1 { animation: burst1 0.6s ease-out forwards; }
        .like-burst-2 { animation: burst2 0.6s ease-out 0.05s forwards; }
        .like-burst-3 { animation: burst3 0.6s ease-out 0.1s forwards; }
        .like-burst-4 { animation: burst4 0.6s ease-out 0.15s forwards; }
        @keyframes burst1 { 0% { transform: translate(0,0) scale(0); opacity: 1; } 100% { transform: translate(-16px,-14px) scale(1); opacity: 0; } }
        @keyframes burst2 { 0% { transform: translate(0,0) scale(0); opacity: 1; } 100% { transform: translate(14px,-12px) scale(1); opacity: 0; } }
        @keyframes burst3 { 0% { transform: translate(0,0) scale(0); opacity: 1; } 100% { transform: translate(-10px,14px) scale(1); opacity: 0; } }
        @keyframes burst4 { 0% { transform: translate(0,0) scale(0); opacity: 1; } 100% { transform: translate(12px,10px) scale(1); opacity: 0; } }
    </style>
</div>
