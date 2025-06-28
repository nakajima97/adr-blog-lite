<?php

namespace Tests\Feature\Feature;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateArticleTest extends TestCase
{
    use RefreshDatabase;

    public function test_記事作成フォームが表示される(): void
    {
        $response = $this->get('/articles/create');

        $response->assertStatus(200);
        $response->assertViewIs('articles.create');
    }

    public function test_記事管理画面に記事作成ボタンが表示される(): void
    {
        $response = $this->get('/articles/manage');

        $response->assertStatus(200);
        $response->assertSee('記事を投稿');
        $response->assertSee('新しい記事を作成');
        $response->assertSee(route('articles.create'));
    }

    public function test_正常な記事データで記事が作成される(): void
    {
        // テスト用ユーザーの作成
        $user = User::factory()->create();

        $articleData = [
            'title' => 'テスト記事タイトル',
            'content' => 'これはテスト記事の内容です。最低10文字以上の内容です。',
            'status' => 'draft',
        ];

        $response = $this->post('/articles', $articleData);

        // リダイレクトの確認
        $response->assertStatus(302);

        // データベースに記事が作成されたことを確認
        $this->assertDatabaseHas('articles', [
            'title' => 'テスト記事タイトル',
            'content' => 'これはテスト記事の内容です。最低10文字以上の内容です。',
            'status' => 'draft',
        ]);
    }

    public function test_タイトルが空の場合はバリデーションエラーになる(): void
    {
        $articleData = [
            'title' => '',
            'content' => 'これはテスト記事の内容です。',
            'status' => 'draft',
        ];

        $response = $this->post('/articles', $articleData);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['title']);
    }

    public function test_本文が10文字未満の場合はバリデーションエラーになる(): void
    {
        $articleData = [
            'title' => 'テストタイトル',
            'content' => '短い',
            'status' => 'draft',
        ];

        $response = $this->post('/articles', $articleData);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['content']);
    }

    public function test_同一タイトルの記事は作成できない(): void
    {
        // 既存記事の作成
        Article::factory()->create([
            'title' => '重複テストタイトル',
        ]);

        $articleData = [
            'title' => '重複テストタイトル',
            'content' => 'これはテスト記事の内容です。最低10文字以上の内容です。',
            'status' => 'draft',
        ];

        $response = $this->post('/articles', $articleData);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['title']);
    }

    public function test_無効なステータスではバリデーションエラーになる(): void
    {
        $articleData = [
            'title' => 'テストタイトル',
            'content' => 'これはテスト記事の内容です。最低10文字以上の内容です。',
            'status' => 'invalid_status',
        ];

        $response = $this->post('/articles', $articleData);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['status']);
    }
}
