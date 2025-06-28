<?php

namespace Tests\Unit\Actions\Article;

use App\Actions\Article\ListArticlesAction;
use App\Models\Article;
use App\Models\User;
use App\Responders\Web\ArticleWebResponder;
use App\UseCases\Article\ListArticlesUseCase;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

/**
 * ListArticlesActionのユニットテスト
 * 
 * ADRパターンのエントリポイントのテスト:
 * - HTTPリクエストの受け取り
 * - パラメータの検証と抽出
 * - UseCaseへの委譲
 * - Responderでのレスポンス生成
 * 
 * 注意: readonly finalクラスはモックできないため、
 * 実際のオブジェクトを使用した統合テスト形式で実装
 */
class ListArticlesActionTest extends TestCase
{
    use RefreshDatabase;

    private ListArticlesAction $action;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $useCase = new ListArticlesUseCase();
        $responder = new ArticleWebResponder();
        $this->action = new ListArticlesAction($useCase, $responder);
        
        // テスト用ユーザー作成
        $this->user = User::factory()->create();
    }

    public function test_デフォルトパラメータで正常に動作する(): void
    {
        // テスト用記事を作成
        Article::factory()->count(5)->create([
            'user_id' => $this->user->id,
            'status' => 'published',
        ]);

        $request = Request::create('/articles', 'GET');
        $result = $this->action->__invoke($request);

        $this->assertInstanceOf(View::class, $result);
        $this->assertEquals('articles.index', $result->getName());
        
        // ビューデータを確認
        $data = $result->getData();
        $this->assertEquals(1, $data['currentPage']);
        $this->assertEquals(10, $data['articles']->perPage());
    }

    public function test_リクエストパラメータが正しく処理される(): void
    {
        // 30件の記事を作成
        Article::factory()->count(30)->create([
            'user_id' => $this->user->id,
            'status' => 'published',
        ]);

        $request = Request::create('/articles?page=2&per_page=20', 'GET');
        $result = $this->action->__invoke($request);

        $this->assertInstanceOf(View::class, $result);
        
        // ビューデータを確認
        $data = $result->getData();
        $this->assertEquals(2, $data['currentPage']);
        $this->assertEquals(20, $data['articles']->perPage());
        $this->assertEquals(2, $data['lastPage']); // 30件÷20件=2ページ
    }

    public function test_負のページ番号が1に補正される(): void
    {
        Article::factory()->count(5)->create([
            'user_id' => $this->user->id,
            'status' => 'published',
        ]);

        $request = Request::create('/articles?page=-5', 'GET');
        $result = $this->action->__invoke($request);

        $this->assertInstanceOf(View::class, $result);
        
        // 負の値は1に補正される
        $data = $result->getData();
        $this->assertEquals(1, $data['currentPage']);
    }

    public function test_ページサイズの範囲制限(): void
    {
        Article::factory()->count(20)->create([
            'user_id' => $this->user->id,
            'status' => 'published',
        ]);

        // 最小値以下のテスト（0は1に補正される）
        $request = Request::create('/articles?per_page=0', 'GET');
        $result = $this->action->__invoke($request);
        $data = $result->getData();
        $this->assertEquals(1, $data['articles']->perPage());

        // 最大値以上のテスト（100は50に制限される）
        $request = Request::create('/articles?per_page=100', 'GET');
        $result = $this->action->__invoke($request);
        $data = $result->getData();
        $this->assertEquals(50, $data['articles']->perPage());
    }

    public function test_不正な値でもエラーにならない(): void
    {
        Article::factory()->count(5)->create([
            'user_id' => $this->user->id,
            'status' => 'published',
        ]);

        $request = Request::create('/articles?page=abc&per_page=xyz', 'GET');
        $result = $this->action->__invoke($request);

        $this->assertInstanceOf(View::class, $result);
        
        // 不正な値は最小値に補正される
        $data = $result->getData();
        $this->assertEquals(1, $data['currentPage']);
        $this->assertEquals(1, $data['articles']->perPage()); // 不正な値は1に補正される
    }

    public function test_小数点を含むパラメータの処理(): void
    {
        Article::factory()->count(25)->create([
            'user_id' => $this->user->id,
            'status' => 'published',
        ]);

        $request = Request::create('/articles?page=2.5&per_page=15.7', 'GET');
        $result = $this->action->__invoke($request);

        $this->assertInstanceOf(View::class, $result);
        
        // 小数点は整数に変換される
        $data = $result->getData();
        $this->assertEquals(2, $data['currentPage']);
        $this->assertEquals(15, $data['articles']->perPage());
    }

    public function test_境界値の処理(): void
    {
        Article::factory()->count(20)->create([
            'user_id' => $this->user->id,
            'status' => 'published',
        ]);

        // ページサイズの境界値テスト
        $testCases = [
            ['per_page' => 1, 'expected' => 1],   // 最小値
            ['per_page' => 50, 'expected' => 50], // 最大値（制限により20件になる）
            ['per_page' => 25, 'expected' => 25], // 中間値
        ];

        foreach ($testCases as $case) {
            $request = Request::create("/articles?per_page={$case['per_page']}", 'GET');
            $result = $this->action->__invoke($request);
            
            $this->assertInstanceOf(View::class, $result);
            
            $data = $result->getData();
            $this->assertEquals($case['expected'], $data['articles']->perPage());
        }
    }
} 