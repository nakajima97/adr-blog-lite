<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * 記事詳細機能の統合テスト
 *
 * ADRパターンで実装された記事詳細機能の
 * エンドツーエンドテストを実行
 */
class ArticleDetailTest extends TestCase
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

    public function test_公開記事詳細ページが正常に表示される(): void
    {
        // 公開記事を作成
        $article = Article::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'published',
            'title' => 'テスト記事のタイトル',
            'content' => 'これはテスト記事の内容です。詳細な記事の内容が表示されます。',
        ]);

        $response = $this->get("/articles/{$article->id}");

        $response->assertStatus(200);
        $response->assertViewIs('articles.show');
        $response->assertSee($article->title);
        $response->assertSee($article->content);
        $response->assertSee($this->user->name);
    }

    public function test_下書き記事詳細ページにアクセスすると404エラー(): void
    {
        // 下書き記事を作成
        $article = Article::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'draft',
        ]);

        $response = $this->get("/articles/{$article->id}");

        $response->assertStatus(404);
    }

    public function test_存在しない記事IDでアクセスすると404エラー(): void
    {
        $response = $this->get('/articles/99999');

        $response->assertStatus(404);
    }

    public function test_記事詳細ページに投稿者情報が表示される(): void
    {
        // 特定の名前のユーザーで記事を作成
        $author = User::factory()->create(['name' => '記事投稿者']);
        $article = Article::factory()->create([
            'user_id' => $author->id,
            'status' => 'published',
        ]);

        $response = $this->get("/articles/{$article->id}");

        $response->assertStatus(200);
        $response->assertSee($author->name);
        $response->assertSee($article->created_at->format('Y年m月d日'));
    }

    public function test_記事詳細ページのパンくずリストが正常に動作する(): void
    {
        $article = Article::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'published',
        ]);

        $response = $this->get("/articles/{$article->id}");

        $response->assertStatus(200);
        // パンくずリストの記事一覧リンクが存在することを確認
        $response->assertSee('記事一覧');
        $response->assertSee(route('articles.index'));
    }

    public function test_記事詳細ページから記事一覧に戻るリンクが動作する(): void
    {
        $article = Article::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'published',
        ]);

        $response = $this->get("/articles/{$article->id}");

        $response->assertStatus(200);
        // 記事一覧に戻るリンクが存在することを確認
        $response->assertSee('記事一覧に戻る');
    }

    public function test_記事一覧から詳細ページへのリンクが正常に動作する(): void
    {
        // 複数の公開記事を作成
        $articles = Article::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'status' => 'published',
        ]);

        // 記事一覧ページを取得
        $response = $this->get('/articles');
        $response->assertStatus(200);

        // 各記事の詳細リンクが存在することを確認
        foreach ($articles as $article) {
            $response->assertSee(route('articles.show', $article->id));
        }
    }

    public function test_無効な記事IDフォーマットでアクセスすると404エラー(): void
    {
        // 非数値の記事IDでアクセス
        $response = $this->get('/articles/invalid-id');

        $response->assertStatus(404);
    }

    public function test_負の記事IDでアクセスすると404エラー(): void
    {
        $response = $this->get('/articles/-1');

        $response->assertStatus(404);
    }

    public function test_複数の記事が存在する場合でも正しい記事が表示される(): void
    {
        // 複数の記事を作成
        $article1 = Article::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'published',
            'title' => '最初の記事',
            'content' => '最初の記事の内容です。',
        ]);

        $article2 = Article::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'published',
            'title' => '二番目の記事',
            'content' => '二番目の記事の内容です。',
        ]);

        // 最初の記事の詳細を確認
        $response1 = $this->get("/articles/{$article1->id}");
        $response1->assertStatus(200);
        $response1->assertSee($article1->title);
        $response1->assertSee($article1->content);
        $response1->assertDontSee($article2->title);

        // 二番目の記事の詳細を確認
        $response2 = $this->get("/articles/{$article2->id}");
        $response2->assertStatus(200);
        $response2->assertSee($article2->title);
        $response2->assertSee($article2->content);
        $response2->assertDontSee($article1->title);
    }
} 