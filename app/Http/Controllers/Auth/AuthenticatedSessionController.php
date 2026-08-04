<?php

namespace App\Http\Controllers\Auth;

use App\Enums\PanelTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{

    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = $request->user();

        // REENVIA A VERIFICAÇÃO, MAS AINDA PODE LOGAR MSM SEM SER VERIFICADO: REMOVA o logout e a invalidação de sessão daqui
        //if (!$user->hasVerifiedEmail()) {
        // Redirecionamos direto para a rota que o Breeze já gerencia
        //return redirect()->route('verification.notice');
        //}

        if (!$user->hasVerifiedEmail()) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['email' => 'Você precisa verificar seu e-mail antes de acessar a plataforma.'])
                ->withInput();
        }

        if ($user->panel === PanelTypeEnum::CHANNEL || $user->panel === PanelTypeEnum::SUPER_ADMIN) {
            return redirect()->intended(route('filament.admin.pages.dashboard'));
        }

        return redirect()->route('app.home');
    }


    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
