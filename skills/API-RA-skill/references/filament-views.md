# Filament PHP — Views e Recursos Administrativos

## 1. Registrar Resources no Panel Provider

```php
// app/Providers/Filament/AdminPanelProvider.php
->resources([
    RaGameResource::class,
    ProjectChallengeResource::class,
])
->pages([
    // páginas customizadas se necessário
])
```

## 2. RaGameResource — Gestão do Pool de Jogos

```php
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RaGameResource\Pages;
use App\Models\RaGame;
use App\Services\RetroAchievements\RetroAchievementsService;
use Filament\Forms\{Form, Components\TextInput, Components\Toggle, Components\Textarea};
use Filament\Tables\{Table, Columns\TextColumn, Columns\ImageColumn, Columns\ToggleColumn, Actions\Action};
use Filament\Resources\Resource;
use Filament\Notifications\Notification;

class RaGameResource extends Resource
{
    protected static ?string $model = RaGame::class;
    protected static ?string $navigationIcon = 'heroicon-o-puzzle-piece';
    protected static ?string $navigationLabel = 'Jogos RA';
    protected static ?string $navigationGroup = 'RetroAchievements';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('ra_game_id')
                ->label('ID do Jogo no RA')
                ->numeric()
                ->required()
                ->helperText('Encontre o ID na URL: retroachievements.org/game/{ID}'),
            TextInput::make('title')->label('Título')->disabled(),
            TextInput::make('console_name')->label('Console')->disabled(),
            TextInput::make('achievement_count')->label('Nº Conquistas')->disabled(),
            Toggle::make('is_in_project_pool')
                ->label('No Pool do Projeto')
                ->helperText('Marque para incluir no sorteio de desafios'),
            Textarea::make('notes')->label('Notas Admin')->rows(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_icon_url')
                    ->label('')
                    ->width(40)
                    ->height(40)
                    ->rounded(),
                TextColumn::make('title')
                    ->label('Jogo')
                    ->searchable()
                    ->sortable()
                    ->url(fn(RaGame $r) => $r->ra_url, true),
                TextColumn::make('console_name')->label('Console')->badge(),
                TextColumn::make('achievement_count')->label('Conquistas')->sortable(),
                TextColumn::make('points')->label('Pontos')->sortable(),
                ToggleColumn::make('is_in_project_pool')->label('No Pool'),
                TextColumn::make('projectChallenges_count')
                    ->label('Canais usando')
                    ->counts('projectChallenges'),
            ])
            ->filters([
                Tables\Filters\Filter::make('in_pool')
                    ->label('Apenas do Pool')
                    ->query(fn($q) => $q->where('is_in_project_pool', true)),
            ])
            ->headerActions([
                Action::make('import_game')
                    ->label('Importar Jogo do RA')
                    ->icon('heroicon-o-cloud-arrow-down')
                    ->form([
                        TextInput::make('game_id')
                            ->label('ID do Jogo RA')
                            ->numeric()
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        $service = app(RetroAchievementsService::class);
                        $game    = $service->importGame((int) $data['game_id']);
                        if ($game) {
                            Notification::make()->title("Jogo importado: {$game->title}")->success()->send();
                        } else {
                            Notification::make()->title('Falha ao importar. Verifique o ID.')->danger()->send();
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('refresh')
                    ->label('Atualizar')
                    ->icon('heroicon-o-arrow-path')
                    ->action(function (RaGame $record) {
                        $service = app(RetroAchievementsService::class);
                        $service->importGame($record->ra_game_id);
                        Notification::make()->title('Dados atualizados!')->success()->send();
                    }),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListRaGames::route('/'),
            'create' => Pages\CreateRaGame::route('/create'),
            'edit'   => Pages\EditRaGame::route('/{record}/edit'),
        ];
    }
}
```

## 3. ProjectChallengeResource — Gestão de Desafios + Sorteio

### Resource

```php
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectChallengeResource\Pages;
use App\Models\{ProjectChallenge, RaGame};
use App\Services\RetroAchievements\RetroAchievementsService;
use App\Jobs\SyncRaProfileJob;
use Filament\Forms\Components\{Select, DateTimePicker, Textarea};
use Filament\Tables\{Table, Actions\Action, Actions\BulkAction, Columns\TextColumn, Columns\BadgeColumn};
use Filament\Resources\Resource;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;

class ProjectChallengeResource extends Resource
{
    protected static ?string $model = ProjectChallenge::class;
    protected static ?string $navigationIcon = 'heroicon-o-trophy';
    protected static ?string $navigationLabel = 'Desafios';
    protected static ?string $navigationGroup = 'RetroAchievements';

    public static function form(\Filament\Forms\Form $form): \Filament\Forms\Form
    {
        return $form->schema([
            Select::make('channel_id')
                ->relationship('channel', 'name')
                ->searchable()
                ->preload()
                ->required(),
            Select::make('ra_game_id')
                ->label('Jogo RA')
                ->options(RaGame::where('is_in_project_pool', true)->pluck('title', 'ra_game_id'))
                ->searchable()
                ->required(),
            Select::make('status')
                ->options([
                    'active'    => 'Ativo',
                    'mastered'  => 'Masterizado',
                    'paused'    => 'Pausado',
                    'cancelled' => 'Cancelado',
                ])
                ->default('active')
                ->required(),
            DateTimePicker::make('deadline_at')->label('Prazo (opcional)'),
            Textarea::make('admin_notes')->label('Notas Admin')->rows(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('channel.name')
                    ->label('Canal')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('raGame.title')
                    ->label('Jogo')
                    ->searchable(),
                TextColumn::make('raGame.console_name')
                    ->label('Console')
                    ->badge()
                    ->color('gray'),
                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'active',
                        'success' => 'mastered',
                        'gray'    => 'paused',
                        'danger'  => 'cancelled',
                    ]),
                TextColumn::make('progress_percent')
                    ->label('Progresso')
                    ->formatStateUsing(fn($state) => "{$state}%")
                    ->sortable(),
                TextColumn::make('drawn_at')->label('Sorteado em')->dateTime('d/m/Y')->sortable(),
                TextColumn::make('completed_at')->label('Concluído em')->dateTime('d/m/Y')->placeholder('—'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active'   => 'Ativo',
                        'mastered' => 'Masterizado',
                        'paused'   => 'Pausado',
                    ]),
            ])
            ->actions([
                Action::make('sync')
                    ->label('Sync RA')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->action(function (ProjectChallenge $record) {
                        $profile = $record->channel->raProfile;
                        if ($profile) {
                            SyncRaProfileJob::dispatch($profile);
                            Notification::make()->title('Sync disparado!')->success()->send();
                        } else {
                            Notification::make()->title('Canal sem perfil RA.')->warning()->send();
                        }
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                BulkAction::make('sync_all')
                    ->label('Sincronizar Selecionados')
                    ->icon('heroicon-o-arrow-path')
                    ->action(function (Collection $records) {
                        $records->each(function ($record) {
                            if ($profile = $record->channel->raProfile) {
                                SyncRaProfileJob::dispatch($profile);
                            }
                        });
                        Notification::make()->title('Sincronizações disparadas!')->success()->send();
                    }),
            ])
            ->headerActions([
                Action::make('draw_game')
                    ->label('🎲 Sortear Jogo')
                    ->color('success')
                    ->url(fn() => static::getUrl('draw')),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListProjectChallenges::route('/'),
            'create' => Pages\CreateProjectChallenge::route('/create'),
            'edit'   => Pages\EditProjectChallenge::route('/{record}/edit'),
            'draw'   => Pages\DrawChallengeGame::route('/draw'),
        ];
    }
}
```

### Página de Sorteio — DrawChallengeGame.php

```php
<?php

namespace App\Filament\Resources\ProjectChallengeResource\Pages;

use App\Filament\Resources\ProjectChallengeResource;
use App\Models\{RaGame, Channel};
use App\Services\RetroAchievements\RetroAchievementsService;
use Filament\Resources\Pages\Page;
use Filament\Forms\{Form, Components\Select};
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Livewire\Component;

class DrawChallengeGame extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = ProjectChallengeResource::class;
    protected static string $view = 'filament.resources.project-challenge.pages.draw-challenge-game';
    protected static ?string $title = '🎲 Sortear Jogo-Desafio';

    public ?int $channelId  = null;
    public ?array $drawnGame = null;
    public bool $confirmed   = false;

    public function form(Form $form): Form
    {
        return $form->schema([
            Select::make('channelId')
                ->label('Canal')
                ->options(
                    Channel::whereHas('raProfile')->pluck('name', 'id')
                )
                ->searchable()
                ->required(),
        ]);
    }

    public function draw(): void
    {
        $this->validate(['channelId' => 'required|exists:channels,id']);

        $pool    = RaGame::where('is_in_project_pool', true)->pluck('ra_game_id')->toArray();
        $service = app(RetroAchievementsService::class);
        $game    = $service->drawChallengeGame($this->channelId, $pool);

        if (!$game) {
            Notification::make()
                ->title('Nenhum jogo disponível!')
                ->body('Todos os jogos do pool já foram sorteados para este canal.')
                ->warning()
                ->send();
            return;
        }

        $this->drawnGame = $game->toArray();
        $this->confirmed = false;
    }

    public function confirm(): void
    {
        if (!$this->drawnGame || !$this->channelId) return;

        $service = app(RetroAchievementsService::class);
        $service->createChallenge($this->channelId, $this->drawnGame['ra_game_id']);

        Notification::make()
            ->title("Desafio criado: {$this->drawnGame['title']}!")
            ->success()
            ->send();

        $this->drawnGame = null;
        $this->channelId = null;
        $this->confirmed = true;
    }

    public function redraw(): void
    {
        $this->drawnGame = null;
        $this->draw();
    }
}
```

### View Blade da Página de Sorteio

```blade
{{-- resources/views/filament/resources/project-challenge/pages/draw-challenge-game.blade.php --}}
<x-filament-panels::page>
    <div class="max-w-2xl mx-auto space-y-6">
        <x-filament::section>
            <x-slot name="heading">Selecione o Canal</x-slot>
            <form wire:submit="draw">
                {{ $this->form }}
                <div class="mt-4">
                    <x-filament::button type="submit" icon="heroicon-o-sparkles" size="lg">
                        🎲 Sortear Jogo
                    </x-filament::button>
                </div>
            </form>
        </x-filament::section>

        @if ($drawnGame)
            <x-filament::section>
                <x-slot name="heading">🎮 Jogo Sorteado!</x-slot>
                <div class="flex gap-4 items-center">
                    <img 
                        src="{{ $drawnGame['image_box_art_url'] ?? $drawnGame['image_icon_url'] }}"
                        alt="{{ $drawnGame['title'] }}"
                        class="w-24 h-24 object-cover rounded-lg"
                    />
                    <div>
                        <h3 class="text-xl font-bold">{{ $drawnGame['title'] }}</h3>
                        <p class="text-gray-500">{{ $drawnGame['console_name'] }}</p>
                        <p class="text-sm mt-1">
                            <span class="text-yellow-500">{{ $drawnGame['achievement_count'] }}</span> conquistas ·
                            <span class="text-blue-400">{{ $drawnGame['points'] }}</span> pontos
                        </p>
                        <a 
                            href="{{ $drawnGame['ra_url'] }}"
                            target="_blank"
                            class="text-xs text-gray-400 hover:text-white mt-1 inline-block"
                        >
                            Ver no RetroAchievements →
                        </a>
                    </div>
                </div>

                <div class="flex gap-3 mt-4">
                    <x-filament::button 
                        wire:click="confirm"
                        icon="heroicon-o-check"
                        color="success"
                    >
                        ✅ Confirmar Desafio
                    </x-filament::button>
                    <x-filament::button 
                        wire:click="redraw"
                        icon="heroicon-o-arrow-path"
                        color="warning"
                    >
                        🔁 Sortear Novamente
                    </x-filament::button>
                </div>
            </x-filament::section>
        @endif

        @if ($confirmed)
            <x-filament::section>
                <div class="text-center py-4">
                    <div class="text-4xl mb-2">🎉</div>
                    <p class="text-green-400 font-semibold">Desafio criado com sucesso!</p>
                    <p class="text-gray-400 text-sm mt-1">O canal foi notificado sobre o novo desafio.</p>
                </div>
            </x-filament::section>
        @endif
    </div>
</x-filament-panels::page>
```

## 4. Widget — Estatísticas do Projeto (Dashboard Filament)

```php
class RaProjectStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Canais com RA', RaProfile::where('active', true)->count())
                ->icon('heroicon-o-users'),
            Stat::make('Desafios Ativos', ProjectChallenge::where('status', 'active')->count())
                ->icon('heroicon-o-play'),
            Stat::make('Platinas do Projeto', ProjectMastery::count())
                ->icon('heroicon-o-trophy')
                ->color('success'),
            Stat::make('Jogos no Pool', RaGame::where('is_in_project_pool', true)->count())
                ->icon('heroicon-o-puzzle-piece'),
        ];
    }
}
```

Registre no Panel Provider:
```php
->widgets([
    RaProjectStatsWidget::class,
])
```
