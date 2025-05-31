<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VerbController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\TranslationController;
// Pastikan controller diimpor dengan benar
use App\Http\Controllers\SearchHistoryController;

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
    
    // Rute khusus user yang sudah login
    // Riwayat pencarian - PERBAIKI DUPLIKASI
    Route::get('/history', [SearchHistoryController::class, 'index'])->name('history');
    Route::post('/history', [SearchHistoryController::class, 'store'])->name('history.store');
    Route::delete('/history/{id}', [SearchHistoryController::class, 'destroy'])->name('history.destroy');
    Route::delete('/history', [SearchHistoryController::class, 'destroyAll'])->name('history.destroy.all');
});
Route::get('/api/search-verb', [ApiController::class, 'searchVerb'])->name('api.searchVerb');
Route::post('/api/translate', [App\Http\Controllers\TranslationController::class, 'translate'])->name('api.translate');

Route::get('/about', function () {
    return view('about', ['title' => 'About Page']);
});

Route::get('/test-translation', function () {
    return view('test-translation', ['title' => 'Test Translation']);
});

// Rute untuk terjemahan dan cek API
// Rute untuk terjemahan
Route::post('/api/translate', [App\Http\Controllers\TranslationController::class, 'translate'])->name('api.translate');
Route::post('/api/translate/batch', [App\Http\Controllers\TranslationController::class, 'batchTranslate'])->name('api.translate.batch');
Route::post('/api/translate/check', [App\Http\Controllers\TranslationController::class, 'checkApi'])->name('api.translate.check');
Route::get('/search-verb', [VerbController::class, 'search'])->name('searchVerb');

