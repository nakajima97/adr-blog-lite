<?php

namespace App\Responders\Web;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;

/**
 * 記事管理Web画面用Responder
 *
 * 管理画面向けのレスポンス整形を担当:
 * - 管理用ビューのレンダリング
 * - フィルタリング情報の整理
 * - ページネーション情報の整理
 * - 管理用表示データの加工
 */
final readonly class ArticleManagementWebResponder
{
    /**
     * 記事管理一覧画面のレスポンス生成
     *
     * @param  LengthAwarePaginator  $articles  ページネーション済み記事データ
     * @param  array  $filters  適用されたフィルタリング条件
     */
    public function toManagementView(LengthAwarePaginator $articles, array $filters = []): View
    {
        return view('articles.management.index', [
            'articles' => $articles,
            'filters' => $filters,
            'totalCount' => $articles->total(),
            'currentPage' => $articles->currentPage(),
            'lastPage' => $articles->lastPage(),
            'hasPages' => $articles->hasPages(),
            'statusOptions' => $this->getStatusOptions(),
            'activeStatusFilter' => $filters['status'] ?? 'all',
            'searchQuery' => $filters['search'] ?? '',
            'dateFrom' => $filters['date_from'] ?? '',
            'dateTo' => $filters['date_to'] ?? '',
        ]);
    }

    /**
     * ステータスフィルタのオプションを取得
     */
    private function getStatusOptions(): array
    {
        return [
            'all' => [
                'label' => '全て',
                'value' => 'all',
            ],
            'published' => [
                'label' => '公開済み',
                'value' => 'published',
            ],
            'draft' => [
                'label' => '下書き',
                'value' => 'draft',
            ],
        ];
    }
}
