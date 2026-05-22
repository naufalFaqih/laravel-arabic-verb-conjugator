<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\TranslationController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public pages
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('landing', ['title' => 'تصرف الفعل - Tashrif Arabic Verbs']);
})->name('landing');

Route::get('/search', function () {
    return view('home', ['title' => 'ArabicMorph - Arabic Conjugation Tool']);
})->name('home');

/*
|--------------------------------------------------------------------------
| Guest-only authentication
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

/*
|--------------------------------------------------------------------------
| Authenticated user routes
|--------------------------------------------------------------------------
| Search history is fully Livewire-driven (App\Livewire\History\Index +
| App\Livewire\Verb\Search auto-saves on search). No controller endpoints
| are required.
*/
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/history', function () {
        return view('history', ['title' => 'Riwayat Pencarian']);
    })->name('history');
});

/*
|--------------------------------------------------------------------------
| API: Verb conjugation (Qutrub)
|--------------------------------------------------------------------------
| Kept for backward compatibility (older clients / debugging). Livewire
| Verb\Search component talks to VerbSearchService directly.
*/
Route::get('/api/search-verb', [ApiController::class, 'searchVerb'])->name('api.searchVerb');

/*
|--------------------------------------------------------------------------
| API: Translation (DeepSeek)
|--------------------------------------------------------------------------
| Used by client-side TranslationEnhanced (resources/js/translation-enhanced.js).
*/
Route::post('/api/translate', [TranslationController::class, 'translate'])->name('api.translate');
Route::post('/api/translate/check', [TranslationController::class, 'checkApi'])->name('api.translate.check');
Route::post('/api/translate/batch', [TranslationController::class, 'batchTranslate'])->name('api.translate.batch');

/*
|--------------------------------------------------------------------------
| Chat (DeepSeek proxy)
|--------------------------------------------------------------------------
*/
Route::post('chat', ChatController::class)->withoutMiddleware(VerifyCsrfToken::class);

/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
| Dashboard is Livewire-driven. Other admin pages remain controller-based.
*/
Route::middleware(['auth', AdminMiddleware::class])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/users/{id}', [AdminController::class, 'userDetail'])->name('user.detail');
    Route::patch('/users/{id}/toggle-admin', [AdminController::class, 'toggleAdmin'])->name('user.toggle-admin');
    Route::get('/monitoring', [AdminController::class, 'monitoring'])->name('monitoring');

    // Legacy AJAX endpoints — now also exposed via Livewire wire:click in
    // App\Livewire\Admin\Dashboard (Refresh / Clear Cache / Optimize).
    Route::post('/clear-cache', [AdminController::class, 'clearCache'])->name('clear-cache');
    Route::post('/optimize', [AdminController::class, 'optimize'])->name('optimize');
});
