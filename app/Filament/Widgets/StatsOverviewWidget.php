<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Channel;
use App\Models\Post;
use App\Models\Campaign;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = -2;

    protected function getStats(): array
    {
        $usersCount = User::count();
        $channelsCount = Channel::count();
        $postsCount = Post::count();
        $campaignsCount = Campaign::count();

        $usersLastMonth = User::where('created_at', '>=', now()->subMonth())->count();
        $channelsLastMonth = Channel::where('created_at', '>=', now()->subMonth())->count();
        $postsLastMonth = Post::where('created_at', '>=', now()->subMonth())->count();

        return [
            Stat::make('Usuários', $usersCount)
                ->description($usersLastMonth . ' novos este mês')
                ->descriptionIcon('heroicon-o-users')
                ->color('info'),
            Stat::make('Canais', $channelsCount)
                ->description($channelsLastMonth . ' novos este mês')
                ->descriptionIcon('heroicon-o-user-group')
                ->color('success'),
            Stat::make('Postagens', $postsCount)
                ->description($postsLastMonth . ' novas este mês')
                ->descriptionIcon('heroicon-o-document-text')
                ->color('warning'),
            Stat::make('Campanhas', $campaignsCount)
                ->description('ativas no sistema')
                ->descriptionIcon('heroicon-o-megaphone')
                ->color('danger'),
        ];
    }
}