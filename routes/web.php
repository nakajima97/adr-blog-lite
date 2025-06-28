<?php

use Illuminate\Support\Facades\Route;
use App\Actions\Article\ListArticlesAction;

// ホームページ
Route::get('/', function () {
    return view('welcome');
})->name('home');

// 記事一覧表示（ADRパターン）
Route::get('/articles', ListArticlesAction::class)->name('articles.index');
