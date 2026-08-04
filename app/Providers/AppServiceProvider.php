<?php

namespace App\Providers;

use Filament\Auth\Notifications\VerifyEmail;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        FilamentView::registerRenderHook(
            PanelsRenderHook::AUTH_LOGIN_FORM_AFTER,
            fn(): string => <<<'HTML'
                <div class="flex justify-end gap-1 text-sm">
                    <span class="text-gray-800 dark:text-gray-200">Precisa de ajuda?</span>
                    <a href="/admin/contato" class="text-primary-500">
                            Fale conosco
                    </a>
                </div>
            HTML

        );

        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage)
                ->subject('Retrô community: Verifique seu endereço de e-mail')
                ->greeting('Olá, ' . $notifiable->name . '!')
                ->line('Obrigado por se cadastrar na nossa comunidade!')
                ->line('Para começar a curtir, comentar e seguir canais, clique no botão abaixo:')
                ->action('Verificar E-mail', $url)
                ->line('Se você não criou esta conta, ignore este e-mail.')
                ->salutation('Atenciosamente, Equipe Hall dos Conquistadores');
        });
    }
}
