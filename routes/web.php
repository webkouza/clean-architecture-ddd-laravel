<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;

Route::get('/', function () {
    return view('simple');
});

// デバッグ用シンプルルート
Route::get('/debug', function () {
    return 'Laravel is working!';
});

// Inertia版（後で使用）
Route::get('/vue', function () {
    return \Inertia\Inertia::render('Test');
});

// 本管理画面
Route::get('/books', [BookController::class, 'index'])->name('books.index');
