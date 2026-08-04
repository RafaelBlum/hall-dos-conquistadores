<?php

use App\Http\Controllers\CampaingController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ChannelController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\WebController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// AUTENTICAÇÃO (BREEZE)
require __DIR__ . '/auth.php';

// PÁGINAS PÚBLICAS
Route::get('/', [WebController::class, 'landing'])->name('app.landing');
Route::get('/landing2', [WebController::class, 'landing2'])->name('app.landing2');
Route::get('/home', [WebController::class, 'home'])->name('app.home');
Route::get('/sobre', [WebController::class, 'about'])->name('app.about');
Route::get('/politicas', [WebController::class, 'policy'])->name('app.policy');
Route::get('/suporte', [WebController::class, 'support'])->name('app.support');
Route::get('/game', [WebController::class, 'gameIntruderUsers'])->name('app.game');
Route::get('/game2', [WebController::class, 'gameIntruderUsersV2'])->name('app.game2');

// PÁGINAS CAMPANHAS
Route::get('/campanhas', [CampaingController::class, 'index'])->name('app.campaings');

// POSTS E CATEGORIAS
Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
Route::get('/posts/{slug}', [PostController::class, 'post'])->name('posts.post');
Route::get('/category/{slug}', [CategoryController::class, 'postsForCategory'])->name('posts.category');

// PÁGINAS CANAIS
Route::get('/canais', [ChannelController::class, 'index'])->name('app.channels');
Route::get('/{slug}', [ChannelController::class, 'show'])->name('my.channel');

// DASHBOARD DO CANAL (LOGADO)
Route::get('/canal/{slug}/dashboard', [ChannelController::class, 'dashboard'])->name('my.channel.dashboard');






Route::middleware('auth')->group(function () {

    // 1. A TELA DE AVISO
    Route::get('verify-email', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    // 2. A AÇÃO DE VERIFICAR
    Route::get('verify-email/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect()->route('app.home')->with('status', 'E-mail verificado!');
    })->middleware(['signed', 'throttle:6,1'])->name('verification.verify');

    // 3. REENVIAR E-MAIL
    Route::post('email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('status', 'verification-link-sent');
    })->middleware('throttle:6,1')->name('verification.send');
});

// FALLBACK
Route::fallback(function () {
    return redirect()->route('app.home');
});
