<?php

namespace App\UseCases\Article\Management;

use App\Models\Article;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

/**
 * 記事管理一覧取得UseCase
 *
 * ビジネスロジック（管理機能）:
 * - 公開・下書き記事の取得（フィルタリング対応）
 * - 複雑な検索条件の処理
 * - 最新順で並び替え
 * - ユーザー情報も併せて取得（N+1問題の解決）
 * - ページネーション対応
 */
final readonly class ListArticlesForManagementUseCase
{
    /**
     * 管理用記事一覧を取得（フィルタリング対応）
     *
     * @param  array  $filters  フィルタリング条件
     * @param  int  $page  ページ番号
     * @param  int  $perPage  1ページあたりの件数
     */
    public function execute(array $filters = [], int $page = 1, int $perPage = 15): LengthAwarePaginator
    {
        $query = Article::query()
            ->with('user:id,name')  // N+1問題対策：ユーザー情報を事前読み込み
            ->latest();            // 最新順

        // フィルタリング条件を適用
        $this->applyFilters($query, $filters);

        return $query->paginate(
            perPage: $perPage,
            page: $page
        );
    }

    /**
     * クエリにフィルタリング条件を適用
     *
     * @param  Builder  $query
     * @param  array  $filters
     */
    private function applyFilters(Builder $query, array $filters): void
    {
        // ステータスフィルタ
        if (isset($filters['status'])) {
            match ($filters['status']) {
                'published' => $query->published(),
                'draft' => $query->where('status', 'draft'),
                'all' => null, // 全記事表示のため条件追加なし
                default => null,
            };
        }

        // 検索フィルタ（タイトル・内容）
        if (isset($filters['search']) && !empty($filters['search'])) {
            $searchTerm = $filters['search'];
            $query->where(function (Builder $q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('content', 'like', "%{$searchTerm}%");
            });
        }

        // 日付範囲フィルタ（作成日基準）
        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }
    }
} 