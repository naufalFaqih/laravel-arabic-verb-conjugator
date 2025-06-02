<?php

use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VerbController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\TranslationController;
// Pastikan controller diimpor dengan benar
use App\Http\Controllers\SearchHistoryController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;

// ...kode lainnya...

Route::get('/', function () {
    return view('home', ['title' => 'تصرف الفعل - Tashrif Kata Kerja Bahasa Arab']);
})->name('home');


// Authentication Routes - hanya bisa diakses jika tidak login (guest)
Route::middleware('guest')->group(function () {
    // Login
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    
    // Register
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

// Protected routes - hanya bisa diakses jika sudah login (auth)
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    // Riwayat pencarian - PERBAIKI DUPLIKASI
    Route::get('/history', [SearchHistoryController::class, 'index'])->name('history');
    Route::post('/history', [SearchHistoryController::class, 'store'])->name('history.store');
    Route::delete('/history/{id}', [SearchHistoryController::class, 'destroy'])->name('history.destroy');
    Route::delete('/history', [SearchHistoryController::class, 'destroyAll'])->name('history.destroy.all');
});
Route::get('/api/search-verb', [ApiController::class, 'searchVerb'])->name('api.searchVerb');

// PERBAIKAN: Tambahkan semua route translation yang diperlukan
Route::prefix('translation')->group(function () {
    Route::post('/translate', [TranslationController::class, 'translate']);
    Route::post('/check-api', [TranslationController::class, 'checkApi']);
    Route::post('/batch-translate', [TranslationController::class, 'batchTranslate']);
});
// Rute untuk terjemahan dan cek API

Route::post('/api/translate', [TranslationController::class, 'translate'])->name('api.translate');
Route::post('/api/translate/check', [TranslationController::class, 'checkApi'])->name('api.translate.check');
Route::post('/api/translate/batch', [TranslationController::class, 'batchTranslate'])->name('api.translate.batch');
Route::get('/search-verb', [VerbController::class, 'search'])->name('searchVerb');

Route::post('chat',ChatController::class) -> withoutMiddleware(VerifyCsrfToken::class);

Route::get('/test-translation', function () {
    return view('test-translation', ['title' => 'Test Translation']);
});