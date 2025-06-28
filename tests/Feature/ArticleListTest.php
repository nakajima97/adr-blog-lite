<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * 記事一覧機能の統合テスト
 * 
 * ADRパターンで実装された記事一覧機能の
 * エンドツーエンドテストを実行
 */
class ArticleListTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // テスト用ユーザーを作成
        $this->user = User::factory()->create([
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
        ]);
    }

    public function test_記事一覧ページが正常に表示される(): void
    {
        // 公開記事を作成
        Article::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'status' => 'published',
        ]);

        $response = $this->get('/articles');

        $response->assertStatus(200);
        $response->assertViewIs('articles.index');
    }

    public function test_公開済み記事のみが表示される(): void
    {
        // 公開記事を作成
        $publishedArticles = Article::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'status' => 'published',
            'title' => '公開記事',
        ]);

        // 下書き記事を作成
        Article::factory()->count(2)->create([
            'user_id' => $this->user->id,
            'status' => 'draft',
            'title' => '下書き記事',
        ]);

        $response = $this->get('/articles');

        $response->assertStatus(200);
        
        // 公開記事のタイトルが表示されることを確認
        foreach ($publishedArticles as $article) {
            $response->assertSee($article->title);
        }
        
        // 下書き記事のタイトルは表示されないことを確認
        $response->assertDontSee('下書き記事');
    }

    public function test_記事に投稿者情報が表示される(): void
    {
        // 記事を作成
        Article::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'published',
            'title' => 'テスト記事',
        ]);

        $response = $this->get('/articles');

        $response->assertStatus(200);
        $response->assertSee('テスト記事');
        $response->assertSee('テストユーザー'); // 投稿者名が表示される
    }

    public function test_最新順で記事が表示される(): void
    {
        // 古い記事
        $oldArticle = Article::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'published',
            'title' => '古い記事',
            'created_at' => now()->subDays(2),
        ]);

        // 新しい記事
        $newArticle = Article::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'published',
            'title' => '新しい記事',
            'created_at' => now()->subDay(),
        ]);

        $response = $this->get('/articles');

        $content = $response->getContent();
        $newPos = strpos($content, '新しい記事');
        $oldPos = strpos($content, '古い記事');

        // 新しい記事が古い記事より前に表示される
        $this->assertLessThan($oldPos, $newPos);
    }

    public function test_ページネーションが正常に動作する(): void
    {
        // 11件の記事を作成（デフォルトのページサイズは10件）
        Article::factory()->count(11)->create([
            'user_id' => $this->user->id,
            'status' => 'published',
        ]);

        // 1ページ目
        $response = $this->get('/articles');
        $response->assertStatus(200);
        $response->assertSee('1 / 2 ページ'); // ページ情報の表示

        // 2ページ目
        $response = $this->get('/articles?page=2');
        $response->assertStatus(200);
        $response->assertSee('2 / 2 ページ');
    }

    public function test_ページサイズパラメータが正常に動作する(): void
    {
        // 15件の記事を作成
        Article::factory()->count(15)->create([
            'user_id' => $this->user->id,
            'status' => 'published',
        ]);

        // ページサイズ5で1ページ目
        $response = $this->get('/articles?per_page=5');
        $response->assertStatus(200);
        $response->assertSee('1 / 3 ページ'); // 15件 ÷ 5件 = 3ページ
    }

    public function test_記事がない場合の表示(): void
    {
        $response = $this->get('/articles');

        $response->assertStatus(200);
        $response->assertSee('記事がありません');
        $response->assertSee('まだ公開されている記事がありません。');
    }

    public function test_不正なページ番号でもエラーにならない(): void
    {
        Article::factory()->count(5)->create([
            'user_id' => $this->user->id,
            'status' => 'published',
        ]);

        // 負の値
        $response = $this->get('/articles?page=-1');
        $response->assertStatus(200);

        // 存在しないページ
        $response = $this->get('/articles?page=999');
        $response->assertStatus(200);
    }

    public function test_ページサイズの範囲制限(): void
    {
        Article::factory()->count(10)->create([
            'user_id' => $this->user->id,
            'status' => 'published',
        ]);

        // 範囲外の値でもエラーにならない
        $response = $this->get('/articles?per_page=0');
        $response->assertStatus(200);

        $response = $this->get('/articles?per_page=100');
        $response->assertStatus(200);
    }
} 