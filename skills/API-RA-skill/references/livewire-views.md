# Livewire Views & Blade Templates

## 1. ra-profile-card — Perfil RA no Canal

### resources/views/livewire/channel/ra-profile-card.blade.php

```blade
<div x-data="{ tab: 'achievements' }">
    @if ($raProfile)
        {{-- Header do Perfil --}}
        <div class="bg-gray-900 rounded-xl p-6 border border-gray-700">
            <div class="flex items-start gap-4">
                <img 
                    src="{{ $raProfile->avatar_url }}" 
                    alt="{{ $raProfile->display_name }}"
                    class="w-20 h-20 rounded-full border-2 border-yellow-500"
                    onerror="this.src='/images/ra-placeholder.png'"
                />
                <div class="flex-1">
                    <a 
                        href="https://retroachievements.org/user/{{ $raProfile->ra_username }}"
                        target="_blank"
                        class="text-xl font-bold text-yellow-400 hover:underline flex items-center gap-1"
                    >
                        {{ $raProfile->display_name }}
                        <x-heroicon-o-arrow-top-right-on-square class="w-4 h-4" />
                    </a>

                    @if ($raProfile->motto)
                        <p class="text-gray-400 text-sm italic mt-1">"{{ $raProfile->motto }}"</p>
                    @endif

                    <div class="flex flex-wrap gap-4 mt-3 text-sm">
                        <div class="text-center">
                            <div class="text-yellow-400 font-bold text-lg">{{ number_format($raProfile->total_points) }}</div>
                            <div class="text-gray-500">Pontos HC</div>
                        </div>
                        <div class="text-center">
                            <div class="text-blue-400 font-bold text-lg">{{ $raProfile->mastery_count }}</div>
                            <div class="text-gray-500">Masteries</div>
                        </div>
                        @if ($raProfile->rank)
                            <div class="text-center">
                                <div class="text-green-400 font-bold text-lg">#{{ number_format($raProfile->rank) }}</div>
                                <div class="text-gray-500">Rank Global</div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Botão sync (apenas dono do canal / admin) --}}
                @if (auth()->user()?->can('sync', $channel))
                    <button 
                        wire:click="syncNow"
                        wire:loading.attr="disabled"
                        class="text-gray-400 hover:text-white transition"
                        title="Sincronizar com RA"
                    >
                        <x-heroicon-o-arrow-path class="w-5 h-5" wire:loading.class="animate-spin" />
                    </button>
                @endif
            </div>

            {{-- Última sincronização --}}
            @if ($raProfile->last_synced_at)
                <p class="text-xs text-gray-600 mt-3">
                    Atualizado {{ $raProfile->last_synced_at->diffForHumans() }}
                </p>
            @endif
        </div>

        {{-- Tabs --}}
        <div class="mt-4 flex gap-2">
            @foreach (['achievements' => 'Conquistas RA', 'challenges' => 'Desafios do Projeto', 'masteries' => 'Platinas do Projeto'] as $tabKey => $tabLabel)
                <button
                    @click="tab = '{{ $tabKey }}'"
                    :class="tab === '{{ $tabKey }}' ? 'bg-yellow-500 text-black' : 'bg-gray-800 text-gray-300'"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition"
                >
                    {{ $tabLabel }}
                </button>
            @endforeach
        </div>

        {{-- Tab: Conquistas RA --}}
        <div x-show="tab === 'achievements'" class="mt-4">
            <livewire:channel.ra-achievement-list :channel="$channel" :key="'ach-'.$channel->id" />
        </div>

        {{-- Tab: Desafios do Projeto --}}
        <div x-show="tab === 'challenges'" class="mt-4">
            @php $challenges = $channel->projectChallenges()->with('raGame')->latest('drawn_at')->get(); @endphp
            @forelse ($challenges as $challenge)
                <div class="bg-gray-800 rounded-lg p-4 flex gap-4 mb-3 border border-gray-700">
                    <img 
                        src="{{ $challenge->raGame->image_icon_url }}"
                        alt="{{ $challenge->raGame->title }}"
                        class="w-12 h-12 rounded"
                    />
                    <div class="flex-1">
                        <div class="flex items-center justify-between">
                            <a href="{{ $challenge->raGame->ra_url }}" target="_blank" class="font-semibold text-white hover:text-yellow-400">
                                {{ $challenge->raGame->title }}
                            </a>
                            <span class="text-xs px-2 py-1 rounded-full {{ match($challenge->status) {
                                'mastered'  => 'bg-green-900 text-green-400',
                                'active'    => 'bg-yellow-900 text-yellow-400',
                                'paused'    => 'bg-gray-700 text-gray-400',
                                default     => 'bg-red-900 text-red-400',
                            } }}">
                                {{ ucfirst($challenge->status) }}
                            </span>
                        </div>
                        <div class="text-sm text-gray-400 mt-1">{{ $challenge->raGame->console_name }}</div>

                        {{-- Barra de progresso --}}
                        <div class="mt-2">
                            <div class="flex justify-between text-xs text-gray-500 mb-1">
                                <span>{{ $challenge->progress_earned }}/{{ $challenge->progress_total }} conquistas</span>
                                <span>{{ $challenge->progress_percent }}%</span>
                            </div>
                            <div class="w-full bg-gray-700 rounded-full h-2">
                                <div 
                                    class="h-2 rounded-full {{ $challenge->status === 'mastered' ? 'bg-green-500' : 'bg-yellow-500' }}"
                                    style="width: {{ $challenge->progress_percent }}%"
                                ></div>
                            </div>
                        </div>

                        @if ($challenge->completed_at)
                            <p class="text-xs text-green-400 mt-1">
                                Masterizado em {{ $challenge->completed_at->format('d/m/Y') }}
                            </p>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-gray-500 text-center py-8">Nenhum desafio sorteado ainda.</p>
            @endforelse
        </div>

        {{-- Tab: Platinas do Projeto --}}
        <div x-show="tab === 'masteries'" class="mt-4">
            <livewire:channel.project-mastery-showcase :channel="$channel" :key="'mast-'.$channel->id" />
        </div>

    @else
        {{-- Estado: sem perfil RA vinculado --}}
        <div class="bg-gray-900 rounded-xl p-8 text-center border border-dashed border-gray-700">
            <x-heroicon-o-trophy class="w-12 h-12 text-gray-600 mx-auto mb-3" />
            <p class="text-gray-400">Nenhum perfil RetroAchievements vinculado.</p>
            @if (auth()->user()?->can('update', $channel))
                <a href="{{ route('channel.settings') }}" class="mt-3 inline-block text-yellow-400 hover:underline text-sm">
                    Vincular minha conta RA →
                </a>
            @endif
        </div>
    @endif
</div>
```

---

## 2. project-challenges-page — Página Pública de Desafios

### resources/views/livewire/pages/project-challenges-page.blade.php

```blade
<div>
    <div class="max-w-6xl mx-auto px-4 py-10">
        <div class="text-center mb-10">
            <h1 class="text-4xl font-bold text-white">🎮 Desafios do Projeto</h1>
            <p class="text-gray-400 mt-2">Jogos sorteados para os canais masterizarem</p>
        </div>

        {{-- Filtros --}}
        <div class="flex flex-col sm:flex-row gap-3 mb-8">
            <input 
                wire:model.live.debounce.300ms="search"
                type="text"
                placeholder="Buscar canal..."
                class="flex-1 bg-gray-800 text-white border border-gray-700 rounded-lg px-4 py-2 focus:outline-none focus:border-yellow-500"
            />
            <select 
                wire:model.live="sortBy"
                class="bg-gray-800 text-white border border-gray-700 rounded-lg px-4 py-2"
            >
                <option value="channel">Ordenar por Canal</option>
                <option value="progress">Ordenar por Progresso</option>
                <option value="recent">Mais Recentes</option>
            </select>
        </div>

        {{-- Grid de Desafios --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($challenges as $challenge)
                <div class="bg-gray-900 rounded-xl border border-gray-800 overflow-hidden hover:border-yellow-600 transition group">
                    {{-- Box Art --}}
                    <div class="relative h-40 bg-gray-800 overflow-hidden">
                        @if ($challenge->raGame->image_box_art_url)
                            <img 
                                src="{{ $challenge->raGame->image_box_art_url }}"
                                alt="{{ $challenge->raGame->title }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                                onerror="this.style.display='none'"
                            />
                        @endif
                        <div class="absolute top-2 right-2">
                            <span class="text-xs px-2 py-1 rounded-full backdrop-blur-sm {{ match($challenge->status) {
                                'mastered'  => 'bg-green-800/80 text-green-300',
                                'active'    => 'bg-yellow-800/80 text-yellow-300',
                                default     => 'bg-gray-800/80 text-gray-300',
                            } }}">
                                @if ($challenge->status === 'mastered') 🏆 Masterizado
                                @elseif ($challenge->status === 'active') 🎮 Em Andamento
                                @else ⏸️ Pausado
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="p-4">
                        {{-- Jogo --}}
                        <div class="flex items-center gap-2 mb-1">
                            <img 
                                src="{{ $challenge->raGame->image_icon_url }}"
                                alt=""
                                class="w-6 h-6 rounded"
                            />
                            <a 
                                href="{{ $challenge->raGame->ra_url }}" 
                                target="_blank"
                                class="font-bold text-white hover:text-yellow-400 text-sm truncate"
                            >
                                {{ $challenge->raGame->title }}
                            </a>
                        </div>
                        <p class="text-xs text-gray-500 mb-3">{{ $challenge->raGame->console_name }}</p>

                        {{-- Canal --}}
                        <a 
                            href="{{ route('channels.show', $challenge->channel) }}"
                            class="flex items-center gap-2 mb-4 group/channel"
                        >
                            <img 
                                src="{{ $challenge->channel->thumbnail_url }}"
                                alt="{{ $challenge->channel->name }}"
                                class="w-8 h-8 rounded-full"
                            />
                            <span class="text-sm text-gray-300 group-hover/channel:text-white font-medium truncate">
                                {{ $challenge->channel->name }}
                            </span>
                        </a>

                        {{-- Progresso --}}
                        <div>
                            <div class="flex justify-between text-xs text-gray-500 mb-1">
                                <span>{{ $challenge->progress_earned }}/{{ $challenge->progress_total }} achvs</span>
                                <span class="{{ $challenge->progress_percent >= 100 ? 'text-green-400' : 'text-yellow-400' }} font-semibold">
                                    {{ $challenge->progress_percent }}%
                                </span>
                            </div>
                            <div class="w-full bg-gray-700 rounded-full h-1.5">
                                <div 
                                    class="h-1.5 rounded-full {{ $challenge->status === 'mastered' ? 'bg-green-500' : 'bg-yellow-500' }}"
                                    style="width: {{ min($challenge->progress_percent, 100) }}%"
                                ></div>
                            </div>
                        </div>

                        @if ($challenge->completed_at)
                            <p class="text-xs text-green-400 mt-2">
                                ✅ Completo em {{ $challenge->completed_at->format('d/m/Y') }}
                            </p>
                        @else
                            <p class="text-xs text-gray-600 mt-2">
                                Sorteado em {{ $challenge->drawn_at?->format('d/m/Y') ?? '—' }}
                            </p>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center py-16 text-gray-500">
                    <x-heroicon-o-face-frown class="w-12 h-12 mx-auto mb-3 opacity-50" />
                    <p>Nenhum desafio encontrado.</p>
                </div>
            @endforelse
        </div>

        {{-- Paginação --}}
        <div class="mt-8">
            {{ $challenges->links() }}
        </div>
    </div>
</div>
```

---

## 3. ProjectMasteryShowcase — Vitrine de Platinas

### resources/views/livewire/channel/project-mastery-showcase.blade.php

```blade
<div>
    @php $masteries = $channel->projectMasteries()->with(['raGame', 'projectChallenge'])->latest('mastered_at')->get(); @endphp

    @if ($masteries->isNotEmpty())
        <h3 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
            🏆 <span>Platinas do Projeto ({{ $masteries->count() }})</span>
        </h3>
        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-3">
            @foreach ($masteries as $mastery)
                <a 
                    href="{{ $mastery->raGame->ra_url }}"
                    target="_blank"
                    title="{{ $mastery->raGame->title }} — Masterizado em {{ $mastery->mastered_at->format('d/m/Y') }}"
                    class="block group"
                >
                    <div class="relative">
                        <img 
                            src="{{ $mastery->raGame->image_icon_url }}"
                            alt="{{ $mastery->raGame->title }}"
                            class="w-full aspect-square object-cover rounded-lg border-2 border-yellow-500/50 group-hover:border-yellow-500 transition"
                        />
                        <div class="absolute -top-1 -right-1 bg-yellow-500 rounded-full w-5 h-5 flex items-center justify-center text-xs">
                            🏆
                        </div>
                    </div>
                    <p class="text-xs text-gray-400 mt-1 truncate text-center">{{ $mastery->raGame->title }}</p>
                </a>
            @endforeach
        </div>
    @else
        <div class="text-center py-8 text-gray-600">
            <p>Nenhuma platina do projeto conquistada ainda.</p>
        </div>
    @endif
</div>
```

---

## Rota para a página pública de desafios

```php
// routes/web.php
Route::get('/desafios', ProjectChallengesPage::class)->name('project.challenges');
```

## Registrando os components Livewire

```php
// app/Providers/AppServiceProvider.php (ou Livewire ServiceProvider)
Livewire::component('channel.ra-profile-card', RaProfileCard::class);
Livewire::component('channel.ra-achievement-list', RaAchievementList::class);
Livewire::component('channel.project-mastery-showcase', ProjectMasteryShowcase::class);
Livewire::component('pages.project-challenges-page', ProjectChallengesPage::class);
```

## Uso no perfil do canal

```blade
{{-- resources/views/channels/show.blade.php --}}
<livewire:channel.ra-profile-card :channel="$channel" />
```
