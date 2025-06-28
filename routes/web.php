<?php

use App\Actions\Article\ListArticlesAction;
use App\Actions\Article\ShowArticleAction;
use App\Actions\Article\Management\ListArticlesForManagementAction;
use Illuminate\Support\Facades\Route;

// ホームページ
Route::get('/', function () {
    return view('welcome');
})->name('home');

// 記事一覧表示（ADRパターン）
Route::get('/articles', ListArticlesAction::class)->name('articles.index');

// 記事詳細表示（ADRパターン）
Route::get('/articles/{id}', ShowArticleAction::class)
    ->name('articles.show')
    ->where('id', '[0-9]+'); // 数値のみ許可

// 記事管理一覧表示（ADRパターン）
Route::get('/articles/manage', ListArticlesForManagementAction::class)
    ->name('articles.management.index');
