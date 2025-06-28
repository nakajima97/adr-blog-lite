<?php

namespace Tests\Unit\Responders\Web;

use App\Models\Article;
use App\Models\User;
use App\Responders\Web\ArticleWebResponder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator as ConcretePaginator;
use Illuminate\View\View;
use Tests\TestCase;

/**
 * ArticleWebResponderのユニットテスト
 *
 * レスポンス整形の単体テスト:
 * - ビューの生成
 * - ページネーション情報の整理
 * - 表示用データの加工
 */
class ArticleWebResponderTest extends TestCase
{
    use RefreshDatabase;

    private ArticleWebResponder $responder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->responder = new ArticleWebResponder;
    }

    public function test_記事一覧ビューを正常に生成する(): void
    {
        $user = User::factory()->create();
        $articles = Article::factory()->count(3)->create([
            'user_id' => $user->id,
            'status' => 'published',
        ]);

        // モックペジネーターを作成
        $paginator = $this->createMockPaginator($articles->toArray(), 1, 10, 3);

        $view = $this->responder->index($paginator);

        $this->assertInstanceOf(View::class, $view);
        $this->assertEquals('articles.index', $view->getName());
    }

    public function test_ビューに正しいデータが渡される(): void
    {
        $user = User::factory()->create();
        $articles = Article::factory()->count(5)->create([
            'user_id' => $user->id,
            'status' => 'published',
        ]);

        // 2ページ目、1ページあたり3件の設定
        $paginator = $this->createMockPaginator($articles->take(3)->toArray(), 2, 3, 5);

        $view = $this->responder->index($paginator);
        $data = $view->getData();

        // 基本データの確認
        $this->assertArrayHasKey('articles', $data);
        $this->assertArrayHasKey('totalCount', $data);
        $this->assertArrayHasKey('currentPage', $data);
        $this->assertArrayHasKey('lastPage', $data);
        $this->assertArrayHasKey('hasPages', $data);

        // データの値の確認
        $this->assertEquals($paginator, $data['articles']);
        $this->assertEquals(5, $data['totalCount']);
        $this->assertEquals(2, $data['currentPage']);
        $this->assertEquals(2, $data['lastPage']); // 5件 ÷ 3件 = 2ページ
        $this->assertTrue($data['hasPages']);
    }

    public function test_ページネーション情報が正しく計算される(): void
    {
        // 1ページ目のテスト
        $paginator = $this->createMockPaginator([], 1, 10, 15);
        $view = $this->responder->index($paginator);
        $data = $view->getData();

        $this->assertEquals(1, $data['currentPage']);
        $this->assertEquals(2, $data['lastPage']); // 15件 ÷ 10件 = 2ページ
        $this->assertTrue($data['hasPages']);

        // 最後のページのテスト
        $paginator = $this->createMockPaginator([], 2, 10, 15);
        $view = $this->responder->index($paginator);
        $data = $view->getData();

        $this->assertEquals(2, $data['currentPage']);
        $this->assertEquals(2, $data['lastPage']);
        $this->assertTrue($data['hasPages']);
    }

    public function test_ページが1つしかない場合(): void
    {
        $paginator = $this->createMockPaginator([], 1, 10, 5); // 5件のみ
        $view = $this->responder->index($paginator);
        $data = $view->getData();

        $this->assertEquals(1, $data['currentPage']);
        $this->assertEquals(1, $data['lastPage']);
        $this->assertFalse($data['hasPages']); // ページが1つなのでfalse
    }

    public function test_記事が0件の場合(): void
    {
        $paginator = $this->createMockPaginator([], 1, 10, 0);
        $view = $this->responder->index($paginator);
        $data = $view->getData();

        $this->assertEquals(0, $data['totalCount']);
        $this->assertEquals(1, $data['currentPage']);
        $this->assertEquals(1, $data['lastPage']);
        $this->assertFalse($data['hasPages']);
    }

    public function test_大量のページがある場合(): void
    {
        // 1000件、1ページ10件 = 100ページ
        $paginator = $this->createMockPaginator([], 50, 10, 1000);
        $view = $this->responder->index($paginator);
        $data = $view->getData();

        $this->assertEquals(1000, $data['totalCount']);
        $this->assertEquals(50, $data['currentPage']);
        $this->assertEquals(100, $data['lastPage']);
        $this->assertTrue($data['hasPages']);
    }

    /**
     * テスト用のモックペジネーターを作成
     */
    private function createMockPaginator(
        array $items,
        int $currentPage,
        int $perPage,
        int $total
    ): LengthAwarePaginator {
        return new ConcretePaginator(
            $items,
            $total,
            $perPage,
            $currentPage,
            [
                'path' => request()->url(),
                'pageName' => 'page',
            ]
        );
    }
}
