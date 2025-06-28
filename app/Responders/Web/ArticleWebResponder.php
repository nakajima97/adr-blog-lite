<?php

namespace App\Responders\Web;

use App\Models\Article;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * 記事Web画面用Responder
 *
 * Web画面向けのレスポンス整形を担当:
 * - ビューのレンダリング
 * - ページネーション情報の整理
 * - 表示用データの加工
 * - リダイレクトレスポンス
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

    /**
     * 記事作成フォーム画面のレスポンス生成
     */
    public function create(): View
    {
        return view('articles.create');
    }

    /**
     * 記事作成成功時のレスポンス生成
     *
     * @param  Article  $article  作成された記事
     */
    public function toCreatedResponse(Article $article): RedirectResponse
    {
        $statusMessage = $article->status === 'published'
            ? '記事が正常に公開されました。'
            : '記事が下書きとして保存されました。';

        return redirect()
            ->route('articles.show', ['id' => $article->id])
            ->with('success', $statusMessage)
            ->with('article_id', $article->id);
    }

    /**
     * 記事作成失敗時のレスポンス生成
     *
     * @param  array  $errors  エラー情報
     */
    public function toCreateFailedResponse(array $errors = []): RedirectResponse
    {
        return redirect()
            ->route('articles.create')
            ->withErrors($errors)
            ->withInput()
            ->with('error', '記事の作成に失敗しました。入力内容を確認してください。');
    }

    /**
     * 一覧画面へのリダイレクトレスポンス生成
     *
     * @param  string  $message  フラッシュメッセージ
     */
    public function toIndexWithMessage(string $message): RedirectResponse
    {
        return redirect()
            ->route('articles.index')
            ->with('info', $message);
    }
}
