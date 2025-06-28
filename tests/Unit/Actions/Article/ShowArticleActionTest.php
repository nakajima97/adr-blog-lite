<?php

namespace Tests\Unit\Actions\Article;

use App\Actions\Article\ShowArticleAction;
use App\Models\Article;
use App\Models\User;
use App\Responders\Web\ArticleWebResponder;
use App\UseCases\Article\ShowArticleUseCase;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

/**
 * ShowArticleActionのユニットテスト
 *
 * ADRパターンのエントリポイントのテスト:
 * - HTTPリクエストとパラメータの受け取り
 * - パラメータの検証と抽出
 * - UseCaseへの委譲
 * - Responderでのレスポンス生成
 *
 * 注意: readonly finalクラスはモックできないため、
 * 実際のオブジェクトを使用した統合テスト形式で実装
 */
class ShowArticleActionTest extends TestCase
{
    use RefreshDatabase;

    private ShowArticleAction $action;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $useCase = new ShowArticleUseCase;
        $responder = new ArticleWebResponder;
        $this->action = new ShowArticleAction($useCase, $responder);

        // テスト用ユーザー作成
        $this->user = User::factory()->create();
    }

    public function test_公開済み記事を正常に表示する(): void
    {
        // 公開済み記事を作成
        $article = Article::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'published',
            'title' => 'テスト記事タイトル',
            'content' => 'テスト記事の内容です。',
        ]);

        $request = Request::create("/articles/{$article->id}", 'GET');
        $result = $this->action->__invoke($request, $article->id);

        $this->assertInstanceOf(View::class, $result);
        $this->assertEquals('articles.show', $result->getName());

        // ビューデータを確認
        $data = $result->getData();
        $this->assertEquals($article->id, $data['article']->id);
        $this->assertEquals($article->title, $data['article']->title);
        $this->assertEquals($this->user->name, $data['author']->name);
    }

    public function test_下書き記事にアクセスすると404エラー(): void
    {
        // 下書き記事を作成
        $article = Article::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'draft',
        ]);

        $request = Request::create("/articles/{$article->id}", 'GET');

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->action->__invoke($request, $article->id);
    }

    public function test_存在しない記事_i_dでアクセスすると404エラー(): void
    {
        $nonExistentId = 99999;
        $request = Request::create("/articles/{$nonExistentId}", 'GET');

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->action->__invoke($request, $nonExistentId);
    }

    public function test_負の記事_i_dでアクセスすると404エラー(): void
    {
        $invalidId = -1;
        $request = Request::create("/articles/{$invalidId}", 'GET');

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->action->__invoke($request, $invalidId);
    }

    public function test_ゼロの記事_i_dでアクセスすると404エラー(): void
    {
        $invalidId = 0;
        $request = Request::create("/articles/{$invalidId}", 'GET');

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $this->action->__invoke($request, $invalidId);
    }

    public function test_投稿者情報が正しく取得される(): void
    {
        // 特定の名前のユーザーで記事を作成
        $specificUser = User::factory()->create(['name' => '特定の投稿者']);
        $article = Article::factory()->create([
            'user_id' => $specificUser->id,
            'status' => 'published',
        ]);

        $request = Request::create("/articles/{$article->id}", 'GET');
        $result = $this->action->__invoke($request, $article->id);

        $data = $result->getData();
        $this->assertEquals('特定の投稿者', $data['author']->name);
        $this->assertEquals($specificUser->id, $data['author']->id);
    }
}
