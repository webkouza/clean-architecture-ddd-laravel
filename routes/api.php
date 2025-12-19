<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// 本管理のAPIルート
Route::prefix('books')->group(function () {
    Route::post('/', [BookController::class, 'store'])->name('api.books.store');
});
