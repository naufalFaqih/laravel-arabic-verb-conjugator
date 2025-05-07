<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VerbController;
use App\Http\Controllers\ApiController;

Route::get('/api/search-verb', [ApiController::class, 'searchVerb'])->name('api.searchVerb');

Route::get('/', function () {
    return view('home', ['title' => 'تصرف الفعل']);
});
Route::get('/about', function () {
    return view('about', ['title' => 'About Page']);
});

//buat 2 route baru untuk blog dan kontak
Route::get('/blog', function () {
    return view('blog', ['title' => 'Blog Page']);
});
Route::get('/kontak', function () {
    return view('kontak', ['title' => 'Kontak Page']);
});

Route::get('/search-verb', [VerbController::class, 'search'])->name('searchVerb');
