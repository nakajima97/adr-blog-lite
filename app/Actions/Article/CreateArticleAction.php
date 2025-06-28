<?php

namespace App\Actions\Article;

use App\Http\Requests\CreateArticleRequest;
use App\Responders\Web\ArticleWebResponder;
use App\UseCases\Article\CreateArticleUseCase;
use Illuminate\Http\RedirectResponse;
use Throwable;

/**
 * 記事作成Action
 *
 * ADRパターンにおけるAction層:
 * - HTTPリクエストの受け取り
 * - フォームリクエストによるバリデーション・CSRF検証
 * - UseCaseへの処理委譲
 * - Responderによるレスポンス生成
 */
final readonly class CreateArticleAction
{
    public function __construct(
        private CreateArticleUseCase $useCase,
        private ArticleWebResponder $responder
    ) {}

    /**
     * 記事作成処理
     *
     * @param  CreateArticleRequest  $request  バリデーション済みリクエスト
     */
    public function __invoke(CreateArticleRequest $request): RedirectResponse
    {
        try {
            // バリデーション済みデータの取得
            $articleData = $request->getArticleData();

            // 記事作成前の最終チェック
            if (! $this->useCase->canCreate($articleData)) {
                return $this->responder->toCreateFailedResponse([
                    'title' => ['記事の作成に失敗しました。入力内容を確認してください。'],
                ]);
            }

            // 記事作成実行
            $article = $this->useCase->execute($articleData);

            // 成功レスポンス
            return $this->responder->toCreatedResponse($article);

        } catch (Throwable $e) {
            // エラーログ出力（実際のプロダクトでは適切なロギング）
            logger()->error('記事作成エラー', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->getArticleData(),
            ]);

            // エラーレスポンス
            return $this->responder->toCreateFailedResponse([
                'general' => ['システムエラーが発生しました。もう一度お試しください。'],
            ]);
        }
    }
}
