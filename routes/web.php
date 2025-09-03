<?php
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('notes');
});

Route::get('/notes', function () {
    return view('notes');
});

Route::get('/login', function () {
    return view('auth');
})->name('login');

Route::get('/register', function () {
    return view('auth');
})->name('register');