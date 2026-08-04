<?php

namespace App\Providers\Filament;

use Filament\Facades\Filament;
use Filament\Pages\Dashboard;
use App\Filament\Pages\Auth\EditProfile;
use App\Filament\Pages\Profile;
use App\Livewire\ContactForm;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Http\Client\Request;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Leandrocfe\FilamentApexCharts\FilamentApexChartsPlugin;
use Filament\Support\Enums\Width;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->routes(function () {
                Route::get('/contato', ContactForm::class);
            })
            ->default()
            ->id('admin')
            ->path('admin')
            ->brandName(config('app.name'))
            ->brandLogo(fn() => view('filament.brand.logo'))
            ->favicon(asset('images/brandname/favicon-hall-dos-conquistadores.png'))
            ->sidebarCollapsibleOnDesktop()
            ->maxContentWidth(Width::Full)
            ->colors([
                'primary' => Color::Amber,
            ])
            ->navigationItems([
                NavigationItem::make('Home')
                    ->url(config('app.url'))
                    ->icon('heroicon-o-globe-asia-australia')
                    ->group('Links')
                    ->sort(5)->openUrlInNewTab(),
            ])
            //->userMenuItems([
            //MenuItem::make()
            //->label('Configurações')
            //->url('')
            //->icon('heroicon-o-cog-6-tooth'),
            //'logout' => MenuItem::make()
            //->label('Sair'),
            //'profile' => MenuItem::make()
            //->label(fn() => Auth::user()->name)
            //->icon('heroicon-o-user-circle')
            //->url(static fn(): string => route(Profile::getRouteName(Filament::getPanel('admin')))),
            //])
            ->userMenuItems([
                'profile' => MenuItem::make()
                    ->label('Meu Perfil')
                    ->url(fn() => Profile::getUrl())
                    ->icon('heroicon-m-user-circle'),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverClusters(in: app_path('Filament/Clusters'), for: 'App\\Filament\\Clusters')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                \App\Filament\Widgets\StatsOverviewWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->unsavedChangesAlerts()
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
