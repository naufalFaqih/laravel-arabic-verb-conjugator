<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home', ['title' => 'Home Page']);
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
