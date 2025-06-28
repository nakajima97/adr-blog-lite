<?php

namespace Tests\Unit\UseCases\Article;

use App\Models\Article;
use App\Models\User;
use App\UseCases\Article\ListArticlesUseCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * ListArticlesUseCaseのユニットテスト
 * 
 * ビジネスロジックの単体テスト:
 * - 公開済み記事の取得
 * - 最新順の並び替え
 * - ページネーション
 * - N+1問題の対策
 */
class ListArticlesUseCaseTest extends TestCase
{
    use RefreshDatabase;

    private ListArticlesUseCase $useCase;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->useCase = new ListArticlesUseCase();
        $this->user = User::factory()->create();
    }

    public function test_公開済み記事のみを取得する(): void
    {
        // 公開記事
        $publishedArticles = Article::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'status' => 'published',
        ]);

        // 下書き記事
        Article::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'status' => 'draft',
        ]);

        $result = $this->useCase->execute();

        // 公開記事のみが取得される
        $this->assertCount(3, $result->items());
        
        // すべて公開ステータスであることを確認
        foreach ($result->items() as $article) {
            $this->assertEquals('published', $article->status);
        }
    }

    public function test_最新順で記事を取得する(): void
    {
        // 異なる日時で記事を作成
        $oldArticle = Article::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'published',
            'created_at' => now()->subDays(3),
        ]);

        $middleArticle = Article::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'published',
            'created_at' => now()->subDays(2),
        ]);

        $newArticle = Article::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'published',
            'created_at' => now()->subDay(),
        ]);

        $result = $this->useCase->execute();
        $articles = $result->items();

        // 最新順（新しいものから古いものへ）で並んでいることを確認
        $this->assertEquals($newArticle->id, $articles[0]->id);
        $this->assertEquals($middleArticle->id, $articles[1]->id);
        $this->assertEquals($oldArticle->id, $articles[2]->id);
    }

    public function test_ページネーションが正常に動作する(): void
    {
        // 15件の記事を作成
        Article::factory()->count(15)->create([
            'user_id' => $this->user->id,
            'status' => 'published',
        ]);

        // 1ページ目（10件）
        $result = $this->useCase->execute(page: 1, perPage: 10);
        
        $this->assertEquals(1, $result->currentPage());
        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(15, $result->total());
        $this->assertEquals(2, $result->lastPage());
        $this->assertCount(10, $result->items());

        // 2ページ目（5件）
        $result = $this->useCase->execute(page: 2, perPage: 10);
        
        $this->assertEquals(2, $result->currentPage());
        $this->assertCount(5, $result->items());
    }

    public function test_ユーザー情報が事前読み込みされる(): void
    {
        // 記事を作成
        Article::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'published',
        ]);

        $result = $this->useCase->execute();
        $articles = collect($result->items());
        $article = $articles->first();

        // ユーザー情報が読み込まれていることを確認
        $this->assertTrue($article->relationLoaded('user'));
        $this->assertEquals($this->user->name, $article->user->name);
    }

    public function test_ページサイズの範囲制限(): void
    {
        Article::factory()->count(20)->create([
            'user_id' => $this->user->id,
            'status' => 'published',
        ]);

        // 最小値のテスト
        $result = $this->useCase->execute(page: 1, perPage: 1);
        $this->assertEquals(1, $result->perPage());
        $this->assertCount(1, $result->items());

        // 最大値のテスト（実装で50件に制限されている）
        $result = $this->useCase->execute(page: 1, perPage: 100);
        $this->assertCount(20, $result->items()); // 20件しかないので全件取得
    }

    public function test_記事がない場合の処理(): void
    {
        $result = $this->useCase->execute();

        $this->assertEquals(0, $result->total());
        $this->assertCount(0, $result->items());
        $this->assertEquals(1, $result->currentPage());
        $this->assertEquals(1, $result->lastPage());
    }

    public function test_不正なページ番号でも正常処理される(): void
    {
        Article::factory()->count(5)->create([
            'user_id' => $this->user->id,
            'status' => 'published',
        ]);

        // 負の値は1に補正される（Action層で処理されるが、念のため確認）
        $result = $this->useCase->execute(page: -1, perPage: 10);
        $this->assertCount(5, $result->items());

        // 存在しないページでも正常処理される
        $result = $this->useCase->execute(page: 999, perPage: 10);
        $this->assertEquals(0, $result->count());
        $this->assertEquals(999, $result->currentPage());
    }
} 