<?php

namespace App\Actions\Article\Management;

use App\Responders\Web\ArticleManagementWebResponder;
use App\UseCases\Article\Management\ListArticlesForManagementUseCase;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/**
 * 記事管理一覧表示Action
 *
 * ADRパターンのエントリポイント（管理機能）:
 * - HTTPリクエストの受け取り
 * - フィルタリングパラメータの検証と抽出
 * - UseCaseへの委譲
 * - Responderでのレスポンス生成
 */
final readonly class ListArticlesForManagementAction
{
    public function __construct(
        private ListArticlesForManagementUseCase $useCase,
        private ArticleManagementWebResponder $responder,
    ) {}

    /**
     * 記事管理一覧表示のメイン処理
     *
     * @param  Request  $request  HTTPリクエスト
     */
    public function __invoke(Request $request): View
    {
        // フィルタリングパラメータの取得と検証
        $filters = $this->extractFilters($request);
        
        // ページネーションパラメータの取得
        $page = max(1, $request->integer('page', 1));
        $perPage = min(50, max(1, $request->integer('per_page', 15))); // 管理画面では少し多めに表示

        // UseCaseでビジネスロジック実行
        $articles = $this->useCase->execute($filters, $page, $perPage);

        // Responderでレスポンス整形
        return $this->responder->toManagementView($articles, $filters);
    }

    /**
     * リクエストからフィルタリングパラメータを抽出・検証
     *
     * @param  Request  $request
     * @return array
     */
    private function extractFilters(Request $request): array
    {
        $filters = [];

        // ステータスフィルタ（published, draft, all）
        $status = $request->input('status');
        if (in_array($status, ['published', 'draft', 'all'], true)) {
            $filters['status'] = $status;
        }

        // 検索クエリ（タイトル・内容）
        $search = $request->input('search');
        if (!empty($search) && is_string($search)) {
            $filters['search'] = trim($search);
        }

        // 日付範囲フィルタ
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        
        if (!empty($dateFrom) && strtotime($dateFrom)) {
            $filters['date_from'] = $dateFrom;
        }
        
        if (!empty($dateTo) && strtotime($dateTo)) {
            $filters['date_to'] = $dateTo;
        }

        return $filters;
    }
} 