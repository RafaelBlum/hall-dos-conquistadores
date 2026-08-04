---
name: retroachievements-laravel
description: >
  Integração completa da API do RetroAchievements (RA) em projetos Laravel com TALL Stack
  (Tailwind, Alpine.js, Livewire, Laravel) e painel administrativo Filament PHP.
  Use esta skill sempre que o usuário precisar integrar RetroAchievements em Laravel,
  criar sistema de conquistas/desafios de games retro, exibir perfis de jogadores RA,
  sortear jogos como desafio para canais/usuários, ou criar páginas de showcase de
  achievements e mastery (platinas). Também cobre: migrações, Models, Services,
  Jobs de sincronização, cache com Redis, Livewire components para perfil público,
  recursos Filament para gestão administrativa e sistema de sorteio de jogos-desafio.
---

# RetroAchievements Laravel Integration Skill

## Contexto do Projeto

Este projeto é uma **plataforma para potencializar canais do YouTube**, onde:
- Canais do YouTube se cadastram na plataforma
- Cada canal pode vincular seu username do RetroAchievements
- Canais recebem **jogos sorteados como desafio** para masterizar (platinar)
- O perfil do canal exibe conquistas do RA + conquistas/platinas do projeto
- Página pública lista todos os jogos-desafio do projeto e os canais participantes

## Estrutura de Arquivos a Gerar

```
app/
├── Services/
│   └── RetroAchievements/
│       ├── RetroAchievementsClient.php      # HTTP Client wrapper
│       ├── RetroAchievementsService.php     # Lógica de negócio principal
│       └── RetroAchievementsSync.php        # Sincronização em batch
├── Models/
│   ├── Channel.php                          # Canal do YouTube (já existente, estender)
│   ├── RaProfile.php                        # Perfil RA vinculado ao canal
│   ├── RaGame.php                           # Jogo do RA (cache local)
│   ├── RaAchievement.php                    # Achievement individual
│   ├── RaUserGame.php                       # Progresso do usuário em um jogo
│   ├── ProjectChallenge.php                 # Desafio/sorteio do projeto
│   └── ProjectMastery.php                   # Platinas conquistadas no projeto
├── Jobs/
│   ├── SyncRaProfileJob.php                 # Sync perfil de um canal
│   ├── SyncRaGameJob.php                    # Sync dados de um jogo RA
│   └── DrawChallengeGameJob.php             # Sorteio de jogo-desafio
├── Livewire/
│   ├── Channel/
│   │   ├── RaProfileCard.php                # Card do perfil RA no canal
│   │   ├── RaAchievementList.php            # Lista de achievements
│   │   └── ProjectMasteryShowcase.php       # Showcase de platinas do projeto
│   └── Pages/
│       └── ProjectChallengesPage.php        # Página pública de desafios
└── Filament/
    └── Resources/
        ├── RaGameResource.php               # Gestão de jogos RA
        ├── ProjectChallengeResource.php     # Gestão de desafios
        └── Pages/
            └── DrawChallengeGame.php        # Página de sorteio

database/migrations/
├── create_ra_profiles_table.php
├── create_ra_games_table.php
├── create_ra_achievements_table.php
├── create_ra_user_games_table.php
├── create_project_challenges_table.php
└── create_project_masteries_table.php
```

## 1. Configuração Inicial

### .env
```env
RA_API_BASE_URL=https://retroachievements.org/API
RA_USERNAME=seu_username_ra
RA_API_KEY=sua_api_key_ra
RA_CACHE_TTL=3600
```

### config/retroachievements.php
```php
<?php

return [
    'base_url'   => env('RA_API_BASE_URL', 'https://retroachievements.org/API'),
    'username'   => env('RA_USERNAME'),
    'api_key'    => env('RA_API_KEY'),
    'cache_ttl'  => env('RA_CACHE_TTL', 3600),
    'rate_limit' => [
        'requests_per_minute' => 30,
    ],
];
```

## 2. HTTP Client — RetroAchievementsClient.php

> Consulte `references/api-endpoints.md` para lista completa de endpoints disponíveis.

```php
<?php

namespace App\Services\RetroAchievements;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\RequestException;

class RetroAchievementsClient
{
    private string $baseUrl;
    private string $username;
    private string $apiKey;
    private int $cacheTtl;

    public function __construct()
    {
        $this->baseUrl  = config('retroachievements.base_url');
        $this->username = config('retroachievements.username');
        $this->apiKey   = config('retroachievements.api_key');
        $this->cacheTtl = config('retroachievements.cache_ttl');
    }

    /**
     * Faz requisição GET para a API do RA com cache automático.
     */
    public function get(string $endpoint, array $params = [], ?int $ttl = null): ?array
    {
        $params = array_merge([
            'z' => $this->username,
            'y' => $this->apiKey,
        ], $params);

        $cacheKey = 'ra:' . $endpoint . ':' . md5(serialize($params));
        $ttl      = $ttl ?? $this->cacheTtl;

        return Cache::remember($cacheKey, $ttl, function () use ($endpoint, $params) {
            try {
                $response = Http::timeout(15)
                    ->get("{$this->baseUrl}/{$endpoint}.php", $params);

                if ($response->failed()) {
                    Log::warning("RA API falhou: {$endpoint}", [
                        'status' => $response->status(),
                        'params' => $params,
                    ]);
                    return null;
                }

                return $response->json();
            } catch (RequestException $e) {
                Log::error("RA API exception: {$e->getMessage()}", compact('endpoint'));
                return null;
            }
        });
    }

    /** Busca perfil básico de um usuário RA */
    public function getUserProfile(string $raUsername): ?array
    {
        return $this->get('API_GetUserProfile', ['u' => $raUsername]);
    }

    /** Conquistas recentes do usuário */
    public function getUserRecentAchievements(string $raUsername, int $count = 50): ?array
    {
        return $this->get('API_GetUserRecentAchievements', [
            'u' => $raUsername,
            'c' => $count,
        ], ttl: 600);
    }

    /** Todos os jogos e progresso de um usuário */
    public function getUserAllCompletionProgress(string $raUsername): ?array
    {
        return $this->get('API_GetUserAllCompletion', ['u' => $raUsername], ttl: 1800);
    }

    /** Progresso detalhado do usuário em um jogo específico */
    public function getUserGameProgress(string $raUsername, int $gameId): ?array
    {
        return $this->get('API_GetGameInfoAndUserProgress', [
            'u' => $raUsername,
            'g' => $gameId,
        ], ttl: 900);
    }

    /** Awards/badges de um usuário (mastery/beaten) */
    public function getUserAwards(string $raUsername): ?array
    {
        return $this->get('API_GetUserAwards', ['u' => $raUsername], ttl: 1800);
    }

    /** Metadados básicos de um jogo */
    public function getGame(int $gameId): ?array
    {
        return $this->get('API_GetGame', ['i' => $gameId], ttl: 86400); // 24h cache para dados estáticos
    }

    /** Metadados estendidos de um jogo (inclui achievements) */
    public function getGameExtended(int $gameId): ?array
    {
        return $this->get('API_GetGameExtended', ['i' => $gameId], ttl: 86400);
    }

    /** Invalida cache para um usuário específico */
    public function invalidateUserCache(string $raUsername): void
    {
        $patterns = [
            "API_GetUserProfile:{$raUsername}",
            "API_GetUserAllCompletion:{$raUsername}",
            "API_GetUserAwards:{$raUsername}",
        ];

        foreach ($patterns as $pattern) {
            Cache::forget('ra:' . md5($pattern));
        }
    }
}
```

## 3. Service — RetroAchievementsService.php

```php
<?php

namespace App\Services\RetroAchievements;

use App\Models\{RaProfile, RaGame, RaUserGame, ProjectChallenge, ProjectMastery};
use Illuminate\Support\Facades\DB;

class RetroAchievementsService
{
    public function __construct(
        private RetroAchievementsClient $client
    ) {}

    /**
     * Sincroniza o perfil RA de um canal com os dados da API.
     * Atualiza RaProfile, RaUserGame e verifica novas masteries do projeto.
     */
    public function syncChannelProfile(RaProfile $raProfile): bool
    {
        $raUsername = $raProfile->ra_username;

        $profile = $this->client->getUserProfile($raUsername);
        if (!$profile) return false;

        DB::transaction(function () use ($raProfile, $profile, $raUsername) {
            $raProfile->update([
                'ra_user_id'       => $profile['ID'],
                'display_name'     => $profile['Username'],
                'motto'            => $profile['Motto'] ?? null,
                'rank'             => $profile['Rank'] ?? null,
                'total_points'     => $profile['TotalPoints'] ?? 0,
                'total_softcore'   => $profile['TotalSoftcorePoints'] ?? 0,
                'avatar_url'       => "https://retroachievements.org{$profile['UserPic']}",
                'last_synced_at'   => now(),
            ]);

            // Sincroniza awards/badges (masteries)
            $awards = $this->client->getUserAwards($raUsername);
            if ($awards && isset($awards['VisibleUserAwards'])) {
                $raProfile->update([
                    'mastery_count'  => collect($awards['VisibleUserAwards'])
                        ->where('AwardType', 'Mastery')
                        ->count(),
                ]);
            }

            // Sincroniza progresso nos jogos-desafio do projeto
            $this->syncProjectChallenges($raProfile, $raUsername);
        });

        return true;
    }

    /**
     * Verifica e atualiza o progresso do canal nos jogos-desafio do projeto.
     */
    public function syncProjectChallenges(RaProfile $raProfile, string $raUsername): void
    {
        $activeChallenges = ProjectChallenge::where('channel_id', $raProfile->channel_id)
            ->where('status', 'active')
            ->with('raGame')
            ->get();

        foreach ($activeChallenges as $challenge) {
            $progress = $this->client->getUserGameProgress($raUsername, $challenge->ra_game_id);
            if (!$progress) continue;

            $totalAchievements  = $progress['NumAchievements'] ?? 0;
            $earnedAchievements = $progress['NumAwardedToUser'] ?? 0;
            $isMastered         = $totalAchievements > 0 && $earnedAchievements >= $totalAchievements;

            $challenge->update([
                'progress_earned'  => $earnedAchievements,
                'progress_total'   => $totalAchievements,
                'progress_percent' => $totalAchievements > 0
                    ? round(($earnedAchievements / $totalAchievements) * 100, 2)
                    : 0,
                'completed_at'     => $isMastered && !$challenge->completed_at ? now() : $challenge->completed_at,
                'status'           => $isMastered ? 'mastered' : 'active',
            ]);

            // Registra mastery do projeto se ainda não existir
            if ($isMastered) {
                ProjectMastery::firstOrCreate([
                    'channel_id'         => $raProfile->channel_id,
                    'project_challenge_id' => $challenge->id,
                ], [
                    'ra_game_id'       => $challenge->ra_game_id,
                    'mastered_at'      => now(),
                    'points_earned'    => $progress['ScoreAchieved'] ?? 0,
                ]);
            }
        }
    }

    /**
     * Sorteia um jogo para um canal como desafio.
     * Evita repetir jogos já sorteados para o mesmo canal.
     */
    public function drawChallengeGame(int $channelId, array $gamePool): ?RaGame
    {
        $alreadySorteados = ProjectChallenge::where('channel_id', $channelId)
            ->pluck('ra_game_id')
            ->toArray();

        $available = collect($gamePool)->reject(fn($gameId) => in_array($gameId, $alreadySorteados));

        if ($available->isEmpty()) return null;

        $drawnGameId = $available->random();
        return RaGame::find($drawnGameId);
    }

    /**
     * Cria um desafio para um canal com o jogo sorteado.
     */
    public function createChallenge(int $channelId, int $raGameId): ProjectChallenge
    {
        return ProjectChallenge::create([
            'channel_id'       => $channelId,
            'ra_game_id'       => $raGameId,
            'status'           => 'active',
            'drawn_at'         => now(),
            'progress_earned'  => 0,
            'progress_total'   => 0,
            'progress_percent' => 0,
        ]);
    }

    /**
     * Busca e persiste metadados de um jogo do RA localmente.
     */
    public function importGame(int $gameId): ?RaGame
    {
        $data = $this->client->getGameExtended($gameId);
        if (!$data) return null;

        return RaGame::updateOrCreate(
            ['ra_game_id' => $gameId],
            [
                'title'              => $data['Title'],
                'console_name'       => $data['ConsoleName'],
                'console_id'         => $data['ConsoleID'],
                'image_icon_url'     => "https://retroachievements.org{$data['ImageIcon']}",
                'image_box_art_url'  => "https://retroachievements.org{$data['ImageBoxArt']}",
                'ra_url'             => "https://retroachievements.org/game/{$gameId}",
                'achievement_count'  => $data['NumAchievements'] ?? 0,
                'points'             => $data['points'] ?? 0,
                'genre'              => $data['Genre'] ?? null,
                'developer'          => $data['Developer'] ?? null,
                'publisher'          => $data['Publisher'] ?? null,
                'release_date'       => $data['Released'] ?? null,
            ]
        );
    }
}
```

## 4. Migrations

> Consulte `references/migrations.md` para o código completo de todas as migrations.

**Tabelas necessárias:**
- `ra_profiles` — vincula canal ao perfil RA
- `ra_games` — cache local de jogos do RA
- `ra_user_games` — progresso do usuário por jogo
- `project_challenges` — jogos sorteados como desafio por canal
- `project_masteries` — platinas conquistadas no projeto

## 5. Jobs de Sincronização

```php
// SyncRaProfileJob.php
class SyncRaProfileJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(private RaProfile $raProfile) {}

    public function handle(RetroAchievementsService $service): void
    {
        $service->syncChannelProfile($this->raProfile);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("SyncRaProfileJob falhou para {$this->raProfile->ra_username}", [
            'error' => $exception->getMessage(),
        ]);
    }
}
```

**Agendamento no `routes/console.php` ou `app/Console/Kernel.php`:**
```php
Schedule::command('ra:sync-all-profiles')->hourly();
```

**Comando Artisan `ra:sync-all-profiles`:**
```php
// Dispara SyncRaProfileJob para todos os perfis ativos
RaProfile::where('active', true)->chunk(50, function ($profiles) {
    foreach ($profiles as $profile) {
        SyncRaProfileJob::dispatch($profile)->onQueue('ra-sync');
    }
});
```

## 6. Livewire Components

### RaProfileCard (perfil do canal)

```php
// app/Livewire/Channel/RaProfileCard.php
class RaProfileCard extends Component
{
    public Channel $channel;
    public ?RaProfile $raProfile;
    public bool $showAchievements = false;

    public function mount(Channel $channel): void
    {
        $this->channel   = $channel;
        $this->raProfile = $channel->raProfile;
    }

    public function syncNow(): void
    {
        if ($this->raProfile) {
            SyncRaProfileJob::dispatch($this->raProfile);
            $this->dispatch('notify', message: 'Sincronização iniciada!');
        }
    }

    public function render(): View
    {
        return view('livewire.channel.ra-profile-card');
    }
}
```

> Consulte `references/livewire-views.md` para templates Blade/Alpine completos.

### ProjectChallengesPage (página pública)

```php
class ProjectChallengesPage extends Component
{
    public string $search = '';
    public string $sortBy = 'channel';

    public function render(): View
    {
        $challenges = ProjectChallenge::query()
            ->with(['channel', 'raGame'])
            ->whereHas('channel', fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->sortBy === 'progress', fn($q) => $q->orderByDesc('progress_percent'))
            ->when($this->sortBy === 'channel', fn($q) => $q->orderBy('channel_id'))
            ->paginate(20);

        return view('livewire.pages.project-challenges-page', compact('challenges'));
    }
}
```

## 7. Filament PHP — Painel Administrativo

### ProjectChallengeResource.php

```php
class ProjectChallengeResource extends Resource
{
    protected static ?string $model = ProjectChallenge::class;
    protected static ?string $navigationIcon = 'heroicon-o-trophy';
    protected static ?string $navigationLabel = 'Desafios do Projeto';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('channel_id')
                ->relationship('channel', 'name')
                ->searchable()
                ->required(),
            Select::make('ra_game_id')
                ->relationship('raGame', 'title')
                ->searchable()
                ->required(),
            Select::make('status')
                ->options([
                    'active'   => 'Ativo',
                    'mastered' => 'Masterizado',
                    'paused'   => 'Pausado',
                ])
                ->default('active'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('channel.name')->label('Canal')->searchable(),
                TextColumn::make('raGame.title')->label('Jogo')->searchable(),
                BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'active',
                        'success' => 'mastered',
                        'gray'    => 'paused',
                    ]),
                TextColumn::make('progress_percent')
                    ->label('Progresso')
                    ->formatStateUsing(fn($state) => "{$state}%"),
                TextColumn::make('drawn_at')->label('Sorteado em')->dateTime(),
            ])
            ->actions([
                Action::make('sync')
                    ->label('Sincronizar')
                    ->icon('heroicon-o-arrow-path')
                    ->action(fn(ProjectChallenge $record) =>
                        SyncRaProfileJob::dispatch($record->channel->raProfile)
                    ),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                BulkAction::make('draw')
                    ->label('Sortear Jogo')
                    ->action(fn(Collection $records) => /* lógica de sorteio em lote */null),
            ]);
    }
}
```

### Página de Sorteio (Filament)

```php
// DrawChallengeGame.php — Page customizada no Filament
class DrawChallengeGame extends Page
{
    protected static string $resource = ProjectChallengeResource::class;
    protected static string $view = 'filament.resources.project-challenge.pages.draw-game';

    public ?int $selectedChannelId = null;
    public ?int $drawnGameId       = null;
    public array $gamePool         = [];

    public function draw(): void
    {
        $this->validate(['selectedChannelId' => 'required|exists:channels,id']);

        $service    = app(RetroAchievementsService::class);
        $drawnGame  = $service->drawChallengeGame($this->selectedChannelId, $this->gamePool);

        if (!$drawnGame) {
            $this->addError('draw', 'Nenhum jogo disponível no pool para este canal.');
            return;
        }

        $service->createChallenge($this->selectedChannelId, $drawnGame->ra_game_id);
        $this->drawnGameId = $drawnGame->ra_game_id;

        $this->notify('success', "Sorteado: {$drawnGame->title}!");
    }
}
```

## 8. Models — Relacionamentos Chave

```php
// Channel.php (extensão)
public function raProfile(): HasOne
{
    return $this->hasOne(RaProfile::class);
}
public function projectChallenges(): HasMany
{
    return $this->hasMany(ProjectChallenge::class);
}
public function projectMasteries(): HasMany
{
    return $this->hasMany(ProjectMastery::class);
}

// RaProfile.php
public function channel(): BelongsTo
{
    return $this->belongsTo(Channel::class);
}

// ProjectChallenge.php
public function raGame(): BelongsTo
{
    return $this->belongsTo(RaGame::class, 'ra_game_id', 'ra_game_id');
}
public function mastery(): HasOne
{
    return $this->hasOne(ProjectMastery::class);
}
```

## 9. Service Provider

Registre o client como singleton em `AppServiceProvider`:

```php
$this->app->singleton(RetroAchievementsClient::class);
$this->app->singleton(RetroAchievementsService::class);
```

## Referências de Suporte

- `references/api-endpoints.md` — Lista completa de endpoints da API RA com parâmetros
- `references/migrations.md` — Código completo de todas as migrations
- `references/livewire-views.md` — Templates Blade + Alpine.js para os components Livewire
- `references/filament-views.md` — Views Blade do painel Filament (sorteio, showcase)

## Checklist de Implementação

- [ ] Publicar `config/retroachievements.php`
- [ ] Adicionar variáveis ao `.env`
- [ ] Rodar migrations
- [ ] Registrar Service Provider
- [ ] Configurar queue worker (`QUEUE_CONNECTION=redis`)
- [ ] Criar comando Artisan `ra:sync-all-profiles`
- [ ] Agendar sync no Scheduler
- [ ] Adicionar rota para a página pública de desafios
- [ ] Registrar Resources no Filament Panel Provider
