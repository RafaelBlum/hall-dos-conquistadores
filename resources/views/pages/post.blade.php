<x-layouts.app>

    <x-partials.navbar />

    {{-- HERO SECTION --}}
    <section
        class="relative overflow-hidden text-white bg-gradient-to-br from-slate-700 via-violet-600 to-slate-700 dark:from-slate-900 dark:via-purple-900 dark:to-slate-900">
        <div class="absolute inset-0 opacity-15 dark:opacity-20">
            <div class="absolute top-20 left-10 w-96 h-96 bg-violet-400 dark:bg-purple-500 rounded-full blur-3xl"></div>
            <div
                class="absolute bottom-10 right-20 w-[500px] h-[500px] bg-sky-400 dark:bg-blue-500 rounded-full blur-3xl">
            </div>
        </div>
        <div class="relative max-w-screen-xl px-4 pt-32 pb-48 mx-auto lg:pt-40 text-center">
            <a href="{{route('posts.category', ['slug' => $post->category->slug])}}"
                class="inline-block px-4 py-1 mb-6 text-sm font-medium rounded-full bg-white/10 text-white/90 border border-white/20 hover:bg-white/20 transition-colors">
                {{$post->category->title}}
            </a>
            <h1
                class="text-3xl lg:text-5xl font-extrabold leading-tight mb-8 max-w-4xl mx-auto bg-gradient-to-r from-white via-violet-200 to-violet-300 dark:from-white dark:via-purple-200 dark:to-purple-400 bg-clip-text text-transparent">
                {{$post->title}}
            </h1>

            {{-- META PILLS --}}
            <div class="flex flex-wrap justify-center gap-3 mb-8">
                <div
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/10 backdrop-blur-sm border border-white/10 text-sm text-white/90">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>{{$post->created_at->format('d/m/Y')}}</span>
                </div>
                <div
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/10 backdrop-blur-sm border border-white/10 text-sm text-white/90">
                    <img src="{{Storage::url($post->author->channel->brand)}}"
                        class="w-5 h-5 rounded-full object-cover">
                    <span>{{$post->author->name}}</span>
                </div>
                <div
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/10 backdrop-blur-sm border border-white/10 text-sm text-white/90">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <span>{{$post->views}}</span>
                </div>
            </div>

            {{-- FEATURED IMAGE --}}
            <div class="max-w-4xl mx-auto">
                <img src="{{Storage::url($post->featured_image_url)}}" alt="{{$post->title}}"
                    class="w-full rounded-2xl shadow-2xl object-cover max-h-[500px] border-4 border-white/10">
            </div>
        </div>
        <div
            class="absolute bottom-0 left-0 right-0 h-56 bg-gradient-to-b from-transparent via-slate-700/50 to-white dark:via-slate-900/50 dark:to-slate-900">
        </div>
    </section>

    {{-- POST CONTENT --}}
    <section class="bg-white dark:bg-slate-900 py-16 -mt-8 relative z-10">
        <div class="max-w-screen-xl px-4 mx-auto">
            <div class="flex flex-col lg:flex-row lg:gap-12">

                {{-- Main Content --}}
                <div class="flex-1 max-w-3xl">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-8">
                        {{$post->subTitle}}
                    </h2>
                    <div class="prose prose-lg dark:prose-invert max-w-none text-gray-700 dark:text-gray-300">
                        {!!$post->content!!}
                    </div>

                    {{-- Like Button --}}
                    <div class="mt-10">
                        <livewire:post-like :post="$post" />
                    </div>

                    {{-- Comments --}}
                    <livewire:post-comments :post="$post" />
                </div>

                {{-- Sidebar --}}
                <div class="w-full lg:w-80 mt-12 lg:mt-0">
                    <div class="sticky top-24 space-y-6">
                        {{-- Author Info --}}
                        <div class="p-6 rounded-2xl bg-gray-50 dark:bg-slate-800">
                            <div class="flex items-center gap-4 mb-4">
                                <img src="{{Storage::url($post->author->channel->brand)}}"
                                    class="w-14 h-14 rounded-full object-cover ring-2 ring-purple-500" />
                                <div>
                                    <a href="{{route('my.channel', ['slug' => $post->author->channel->slug])}}"
                                        class="text-lg font-bold text-gray-900 dark:text-white hover:text-purple-600 transition-colors">
                                        {{$post->author->channel->title}}
                                    </a>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{$post->author->channel->name}}
                                    </p>
                                </div>
                            </div>
                            <div
                                class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400 mb-4">
                                <span>{{$post->created_at->format('d M Y')}}</span>
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    {{$post->views}}
                                </span>
                            </div>
                            <a href="{{route('posts.category', ['slug' => $post->category->slug])}}"
                                class="block text-center w-full px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-semibold rounded-xl transition-colors">
                                {{$post->category->title}}
                            </a>
                        </div>

                        {{-- Channel Card --}}
                        <a href="{{route('my.channel', ['slug' => $post->author->channel->slug])}}"
                            class="block p-6 rounded-2xl bg-gray-50 dark:bg-slate-800 hover:shadow-xl transition-all duration-300 hover:-translate-y-1 group">
                            <div
                                class="w-20 h-20 mx-auto mb-4 rounded-2xl overflow-hidden ring-4 ring-gray-200 dark:ring-slate-700 group-hover:ring-purple-500 transition-all">
                                <img src="{{Storage::url($post->author->channel->brand)}}"
                                    class="w-full h-full object-cover" alt="{{$post->author->channel->title}}">
                            </div>
                            <h3
                                class="text-center text-lg font-bold text-gray-900 dark:text-white group-hover:text-purple-600 transition-colors">
                                {{$post->author->channel->title}}
                            </h3>
                            <p class="text-center text-sm text-gray-500 dark:text-gray-400 mt-1">
                                {{$post->author->channel->name}}</p>
                            <div class="mt-4 text-center">
                                <span
                                    class="inline-flex items-center gap-2 text-sm text-red-500 hover:text-red-600 transition-colors">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M23.5 6.2c-.3-1-1-1.8-2-2.1C19.6 3.5 12 3.5 12 3.5s-7.6 0-9.5.6c-1 .3-1.7 1.1-2 2.1C0 8.1 0 12 0 12s0 3.9.5 5.8c.3 1 1 1.8 2 2.1 1.9.6 9.5.6 9.5.6s7.6 0 9.5-.6c1-.3 1.7-1.1 2-2.1.5-1.9.5-5.8.5-5.8s0-3.9-.5-5.8zM9.5 15.6V8.4l6.3 3.6-6.3 3.6z" />
                                    </svg>
                                    YouTube
                                </span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <x-partials.footer />

</x-layouts.app>