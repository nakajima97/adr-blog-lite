<?php

namespace App\Responders\Web;

use App\Models\Article;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;

/**
 * 記事Web画面用Responder
 *
 * Web画面向けのレスポンス整形を担当:
 * - ビューのレンダリング
 * - ページネーション情報の整理
 * - 表示用データの加工
 */
final readonly class ArticleWebResponder
{
    /**
     * 記事一覧画面のレスポンス生成
     *
     * @param  LengthAwarePaginator  $articles  ページネーション済み記事データ
     */
    public function index(LengthAwarePaginator $articles): View
    {
        return view('articles.index', [
            'articles' => $articles,
            'totalCount' => $articles->total(),
            'currentPage' => $articles->currentPage(),
            'lastPage' => $articles->lastPage(),
            'hasPages' => $articles->hasPages(),
        ]);
    }

    /**
     * 記事詳細画面のレスポンス生成
     *
     * @param  Article  $article  記事データ（投稿者情報含む）
     */
    public function show(Article $article): View
    {
        return view('articles.show', [
            'article' => $article,
            'author' => $article->user,
        ]);
    }
}
