<?php

namespace App\Http\Controllers\Auth;

use App\Enums\PanelTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\User;
use Filament\Auth\Notifications\VerifyEmail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    { //        $response = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
//            'secret' => config('services.turnstile.secret'),
//            'response' => $request->input('cf-turnstile-response'),
//            'remoteip' => $request->ip(),
//        ]);
//
//        if (!$response->json('success')) {
//            return back()->withErrors(['captcha' => 'Falha na verificação de robô.']);
//        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'panel' => PanelTypeEnum::SUBSCRIBER,
        ]);

        event(new Registered($user));

        if ($user->email_verified_at === null) {
            Auth::login($user);
        }

        return redirect()->route('verification.notice');
    }
}
