<?php

namespace App\Actions\Article;

use App\Responders\Web\ArticleWebResponder;
use Illuminate\Contracts\View\View;

/**
 * 記事作成フォーム表示Action
 *
 * ADRパターンにおけるAction層:
 * - GET /articles/create への対応
 * - フォーム表示のためのビューレンダリング
 */
final readonly class ShowCreateFormAction
{
    public function __construct(
        private ArticleWebResponder $responder
    ) {}

    /**
     * 記事作成フォーム表示
     */
    public function __invoke(): View
    {
        return $this->responder->create();
    }
}
