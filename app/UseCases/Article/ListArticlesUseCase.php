<?php

namespace App\UseCases\Article;

use App\Models\Article;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * 記事一覧取得UseCase
 *
 * ビジネスロジック:
 * - 公開済み記事のみ取得
 * - 最新順で並び替え
 * - ユーザー情報も併せて取得（N+1問題の解決）
 * - ページネーション対応
 */
final readonly class ListArticlesUseCase
{
    /**
     * 公開済み記事一覧を取得
     *
     * @param  int  $page  ページ番号
     * @param  int  $perPage  1ページあたりの件数
     */
    public function execute(int $page = 1, int $perPage = 10): LengthAwarePaginator
    {
        return Article::query()
            ->published()           // 公開済み記事のみ
            ->latest()             // 最新順
            ->with('user:id,name')  // N+1問題対策：ユーザー情報を事前読み込み
            ->paginate(
                perPage: $perPage,
                page: $page
            );
    }
}
