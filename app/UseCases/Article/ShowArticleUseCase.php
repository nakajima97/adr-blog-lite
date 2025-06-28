<?php

namespace App\UseCases\Article;

use App\Models\Article;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * 記事詳細取得UseCase
 *
 * ビジネスロジック:
 * - 指定されたIDの記事を取得
 * - 公開済み記事のみ表示可能
 * - 存在しない記事や下書き記事へのアクセスで404エラー
 * - 投稿者情報も併せて取得（N+1問題の解決）
 */
final readonly class ShowArticleUseCase
{
    /**
     * 記事詳細を取得
     *
     * @param  int  $id  記事ID
     *
     * @throws ModelNotFoundException 記事が見つからない、または非公開の場合
     */
    public function execute(int $id): Article
    {
        // 公開済み記事のみを対象に検索し、投稿者情報も併せて取得
        $article = Article::query()
            ->published()           // 公開済み記事のみ
            ->with('user:id,name')  // N+1問題対策：ユーザー情報を事前読み込み
            ->find($id);

        // 記事が見つからない場合は404エラー
        if ($article === null) {
            abort(404, '記事が見つかりません');
        }

        return $article;
    }
}
