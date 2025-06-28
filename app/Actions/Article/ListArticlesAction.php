<?php

namespace App\Actions\Article;

use App\Responders\Web\ArticleWebResponder;
use App\UseCases\Article\ListArticlesUseCase;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/**
 * 記事一覧表示Action
 *
 * ADRパターンのエントリポイント:
 * - HTTPリクエストの受け取り
 * - パラメータの検証と抽出
 * - UseCaseへの委譲
 * - Responderでのレスポンス生成
 */
final readonly class ListArticlesAction
{
    public function __construct(
        private ListArticlesUseCase $useCase,
        private ArticleWebResponder $responder,
    ) {}

    /**
     * 記事一覧表示のメイン処理
     *
     * @param  Request  $request  HTTPリクエスト
     */
    public function __invoke(Request $request): View
    {
        // クエリパラメータの取得（デフォルト値設定）
        $page = max(1, $request->integer('page', 1));
        $perPage = min(50, max(1, $request->integer('per_page', 10))); // 1-50件の範囲で制限

        // UseCaseでビジネスロジック実行
        $articles = $this->useCase->execute($page, $perPage);

        // Responderでレスポンス整形
        return $this->responder->index($articles);
    }
}
