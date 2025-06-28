<?php

namespace App\Actions\Article;

use App\Responders\Web\ArticleWebResponder;
use App\UseCases\Article\ShowArticleUseCase;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/**
 * 記事詳細表示Action
 *
 * ADRパターンのエントリポイント:
 * - HTTPリクエストとパラメータの受け取り
 * - パラメータの検証と抽出
 * - UseCaseへの委譲
 * - Responderでのレスポンス生成
 */
final readonly class ShowArticleAction
{
    public function __construct(
        private ShowArticleUseCase $useCase,
        private ArticleWebResponder $responder,
    ) {}

    /**
     * 記事詳細表示のメイン処理
     *
     * @param  Request  $request  HTTPリクエスト
     * @param  int  $id  記事ID
     */
    public function __invoke(Request $request, int $id): View
    {
        // パラメータバリデーション（正の整数であることを確認）
        if ($id <= 0) {
            abort(404, '記事が見つかりません');
        }

        // UseCaseでビジネスロジック実行
        $article = $this->useCase->execute($id);

        // Responderでレスポンス整形
        return $this->responder->show($article);
    }
}
