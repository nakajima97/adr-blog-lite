# テスト戦略書（ADRパターン学習重点）

## 概要

ADR Blog Lite のテスト戦略書です。ADRパターン（Action-Domain-Responder）における効果的なテスト手法の学習に重点を置いた内容となっています。

## 🎯 テストの学習目標

### ADRパターンでのテスト手法習得
1. **Action層のテスト**: HTTPリクエスト/レスポンスのテスト
2. **UseCase層のテスト**: ビジネスロジックの単体テスト
3. **Responder層のテスト**: レスポンス形式のテスト
4. **結合テスト**: 層間の連携テスト

### テスト駆動開発の実践
1. テストファーストでの開発
2. 適切なテストカバレッジの確保
3. 保守性の高いテストコードの作成
4. モック・スタブの効果的な活用

## 📊 テスト戦略概要

### テストピラミッド（ADR版）

```
        /\
       /  \
      /E2E \ ← 少数の重要な機能テスト
     /______\
    /        \
   / Feature  \ ← 機能単位の結合テスト
  /____________\
 /              \
/ Unit (A/U/R)   \ ← 各層の単体テスト
/________________\
```

### テスト方針

| テストレベル | 対象 | 目的 | 比率 |
|-------------|------|------|------|
| **Unit Test** | Action, UseCase, Responder | 各層の責務テスト | 70% |
| **Feature Test** | HTTP エンドポイント | ADR連携テスト | 25% |
| **E2E Test** | ユーザーシナリオ | 主要機能テスト | 5% |

## 🧪 単体テスト戦略

### Action層のテスト

#### テスト対象と観点
- HTTPリクエストの受け取り
- バリデーション処理
- UseCaseへの適切な委譲
- Responderからのレスポンス受け取り

#### テスト実装例

```php
<?php
// tests/Unit/Actions/Articles/IndexActionTest.php

namespace Tests\Unit\Actions\Articles;

use App\Actions\Articles\IndexAction;
use App\Http\Requests\Articles\IndexRequest;
use App\UseCases\Articles\GetPublishedArticlesUseCase;
use App\Responders\Api\ArticleResponder;
use Tests\TestCase;
use Mockery;

/**
 * 記事一覧Action単体テスト
 * 
 * 【学習ポイント】
 * - Actionの責務に集中したテスト
 * - 依存関係のモック化
 * - HTTPリクエスト/レスポンスの検証
 */
final class IndexActionTest extends TestCase
{
    private IndexAction $action;
    private Mockery\MockInterface $useCase;
    private Mockery\MockInterface $responder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->useCase = Mockery::mock(GetPublishedArticlesUseCase::class);
        $this->responder = Mockery::mock(ArticleResponder::class);
        $this->action = new IndexAction($this->useCase, $this->responder);
    }

    /**
     * @test
     */
    public function 正常な記事一覧取得(): void
    {
        // Arrange
        $request = new IndexRequest([
            'page' => 1,
            'per_page' => 10,
        ]);

        $mockPaginator = $this->createMockPaginator();
        $expectedResponse = response()->json(['status' => 'success']);

        $this->useCase->shouldReceive('execute')
            ->once()
            ->with(1, 10)
            ->andReturn($mockPaginator);

        $this->responder->shouldReceive('index')
            ->once()
            ->with($mockPaginator)
            ->andReturn($expectedResponse);

        // Act
        $response = ($this->action)($request);

        // Assert
        $this->assertEquals($expectedResponse, $response);
    }

    /**
     * @test
     */
    public function デフォルト値での記事一覧取得(): void
    {
        // Arrange
        $request = new IndexRequest();
        $mockPaginator = $this->createMockPaginator();
        $expectedResponse = response()->json(['status' => 'success']);

        $this->useCase->shouldReceive('execute')
            ->once()
            ->with(1, 10) // デフォルト値
            ->andReturn($mockPaginator);

        $this->responder->shouldReceive('index')
            ->once()
            ->andReturn($expectedResponse);

        // Act
        $response = ($this->action)($request);

        // Assert
        $this->assertEquals($expectedResponse, $response);
    }

    /**
     * @test
     */
    public function UseCaseの例外を適切に伝播する(): void
    {
        // Arrange
        $request = new IndexRequest();
        $exception = new \Exception('UseCase error');

        $this->useCase->shouldReceive('execute')
            ->once()
            ->andThrow($exception);

        // Act & Assert
        $this->expectExceptionObject($exception);
        ($this->action)($request);
    }

    private function createMockPaginator(): Mockery\MockInterface
    {
        return Mockery::mock(\Illuminate\Pagination\LengthAwarePaginator::class);
    }
}
```

**学習ポイント**:
- Actionは薄い層なので、依存関係の呼び出しをテスト
- モックを使って外部依存を排除
- 例外の伝播もテスト対象

### UseCase層のテスト

#### テスト対象と観点
- ビジネスロジックの正確性
- データベース操作
- 例外処理
- トランザクション処理

#### テスト実装例

```php
<?php
// tests/Unit/UseCases/Articles/GetPublishedArticlesUseCaseTest.php

namespace Tests\Unit\UseCases\Articles;

use App\UseCases\Articles\GetPublishedArticlesUseCase;
use App\Models\Article;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * 公開記事取得UseCase単体テスト
 * 
 * 【学習ポイント】
 * - ビジネスロジックに集中したテスト
 * - データベースとの連携テスト
 * - Eloquentクエリの検証
 */
final class GetPublishedArticlesUseCaseTest extends TestCase
{
    use RefreshDatabase;

    private GetPublishedArticlesUseCase $useCase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->useCase = new GetPublishedArticlesUseCase();
    }

    /**
     * @test
     */
    public function 公開記事のみを最新順で取得する(): void
    {
        // Arrange
        $user = User::factory()->create();
        
        $publishedArticle1 = Article::factory()->create([
            'user_id' => $user->id,
            'status' => 'published',
            'created_at' => now()->subDays(2),
        ]);
        
        $publishedArticle2 = Article::factory()->create([
            'user_id' => $user->id,
            'status' => 'published',
            'created_at' => now()->subDays(1),
        ]);
        
        // 下書き記事（取得対象外）
        Article::factory()->create([
            'user_id' => $user->id,
            'status' => 'draft',
        ]);

        // Act
        $result = $this->useCase->execute(page: 1, perPage: 10);

        // Assert
        $this->assertCount(2, $result->items());
        $this->assertEquals($publishedArticle2->id, $result->items()[0]->id); // 最新順
        $this->assertEquals($publishedArticle1->id, $result->items()[1]->id);
        
        // リレーションがロードされていることを確認
        $this->assertTrue($result->items()[0]->relationLoaded('user'));
    }

    /**
     * @test
     */
    public function ページネーションが正しく動作する(): void
    {
        // Arrange
        $user = User::factory()->create();
        Article::factory()->count(25)->create([
            'user_id' => $user->id,
            'status' => 'published',
        ]);

        // Act
        $result = $this->useCase->execute(page: 2, perPage: 10);

        // Assert
        $this->assertEquals(2, $result->currentPage());
        $this->assertEquals(10, $result->perPage());
        $this->assertEquals(25, $result->total());
        $this->assertEquals(3, $result->lastPage());
        $this->assertCount(10, $result->items());
    }

    /**
     * @test
     */
    public function 記事が存在しない場合は空の結果を返す(): void
    {
        // Act
        $result = $this->useCase->execute();

        // Assert
        $this->assertCount(0, $result->items());
        $this->assertEquals(0, $result->total());
    }

    /**
     * @test
     */
    public function N_plus_1問題が発生していないことを確認(): void
    {
        // Arrange
        $users = User::factory()->count(3)->create();
        foreach ($users as $user) {
            Article::factory()->count(2)->create([
                'user_id' => $user->id,
                'status' => 'published',
            ]);
        }

        // Act & Assert
        $queryCount = \DB::getQueryLog();
        \DB::enableQueryLog();
        
        $result = $this->useCase->execute();
        
        $queries = \DB::getQueryLog();
        \DB::disableQueryLog();

        // 記事取得 + ユーザー取得 = 2クエリ以内であることを確認
        $this->assertLessThanOrEqual(2, count($queries));
        
        // リレーションがロードされていることを確認
        foreach ($result->items() as $article) {
            $this->assertTrue($article->relationLoaded('user'));
        }
    }
}
```

**学習ポイント**:
- 実際のデータベースを使用してビジネスロジックをテスト
- パフォーマンス面（N+1問題）の検証
- 境界値やエッジケースのテスト

### Responder層のテスト

#### テスト対象と観点
- レスポンス形式の統一性
- データ変換の正確性
- HTTPステータスコード
- JSON構造の検証

#### テスト実装例

```php
<?php
// tests/Unit/Responders/Api/ArticleResponderTest.php

namespace Tests\Unit\Responders\Api;

use App\Responders\Api\ArticleResponder;
use App\Models\Article;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * 記事レスポンダー単体テスト
 * 
 * 【学習ポイント】
 * - レスポンス形式の検証
 * - データ変換ロジックのテスト
 * - JSON構造の詳細確認
 */
final class ArticleResponderTest extends TestCase
{
    use RefreshDatabase;

    private ArticleResponder $responder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->responder = new ArticleResponder();
    }

    /**
     * @test
     */
    public function index_正しいJSON形式で記事一覧を返す(): void
    {
        // Arrange
        $user = User::factory()->create();
        $articles = Article::factory()->count(3)->create(['user_id' => $user->id]);
        
        $paginator = new LengthAwarePaginator(
            items: $articles->load('user'),
            total: 3,
            perPage: 10,
            currentPage: 1
        );

        // Act
        $response = $this->responder->index($paginator);

        // Assert
        $response->assertStatus(200);
        
        $data = $response->getData(true);
        
        $this->assertEquals('success', $data['status']);
        $this->assertEquals('記事一覧を取得しました', $data['message']);
        $this->assertArrayHasKey('data', $data);
        $this->assertArrayHasKey('articles', $data['data']);
        $this->assertArrayHasKey('pagination', $data['data']);
        
        // 記事データの構造確認
        $articleData = $data['data']['articles'][0];
        $this->assertArrayHasKey('id', $articleData);
        $this->assertArrayHasKey('title', $articleData);
        $this->assertArrayHasKey('content', $articleData);
        $this->assertArrayHasKey('status', $articleData);
        $this->assertArrayHasKey('created_at', $articleData);
        $this->assertArrayHasKey('updated_at', $articleData);
        $this->assertArrayHasKey('author', $articleData);
        
        // 著者データの構造確認
        $this->assertArrayHasKey('id', $articleData['author']);
        $this->assertArrayHasKey('name', $articleData['author']);
        
        // ページネーション情報の確認
        $pagination = $data['data']['pagination'];
        $this->assertEquals(1, $pagination['current_page']);
        $this->assertEquals(10, $pagination['per_page']);
        $this->assertEquals(3, $pagination['total']);
    }

    /**
     * @test
     */
    public function show_正しいJSON形式で記事詳細を返す(): void
    {
        // Arrange
        $user = User::factory()->create();
        $article = Article::factory()->create([
            'user_id' => $user->id,
            'title' => 'Test Article',
            'content' => '# Test Content\n\nThis is a test.',
        ]);
        $article->load('user');

        // Act
        $response = $this->responder->show($article);

        // Assert
        $response->assertStatus(200);
        
        $data = $response->getData(true);
        
        $this->assertEquals('success', $data['status']);
        $this->assertEquals('記事詳細を取得しました', $data['message']);
        
        $articleData = $data['data']['article'];
        $this->assertEquals('Test Article', $articleData['title']);
        $this->assertEquals('# Test Content\n\nThis is a test.', $articleData['content']);
        $this->assertStringContains('<h1>Test Content</h1>', $articleData['content_html']);
        $this->assertEquals($user->name, $articleData['author']['name']);
    }

    /**
     * @test
     */
    public function created_201ステータスで記事作成レスポンスを返す(): void
    {
        // Arrange
        $user = User::factory()->create();
        $article = Article::factory()->create(['user_id' => $user->id]);

        // Act
        $response = $this->responder->created($article);

        // Assert
        $response->assertStatus(201);
        
        $data = $response->getData(true);
        
        $this->assertEquals('success', $data['status']);
        $this->assertEquals('記事を作成しました', $data['message']);
        $this->assertArrayHasKey('article', $data['data']);
    }

    /**
     * @test
     */
    public function コンテンツの切り詰め処理が正しく動作する(): void
    {
        // Arrange
        $user = User::factory()->create();
        $longContent = str_repeat('長い文章のテスト。', 100); // 約1000文字
        $article = Article::factory()->create([
            'user_id' => $user->id,
            'content' => $longContent,
        ]);
        
        $paginator = new LengthAwarePaginator(
            items: collect([$article->load('user')]),
            total: 1,
            perPage: 10,
            currentPage: 1
        );

        // Act
        $response = $this->responder->index($paginator);

        // Assert
        $data = $response->getData(true);
        $truncatedContent = $data['data']['articles'][0]['content'];
        
        $this->assertLessThanOrEqual(200, mb_strlen($truncatedContent));
        $this->assertStringEndsWith('...', $truncatedContent);
    }

    /**
     * @test
     */
    public function ISO8601形式で日時が返される(): void
    {
        // Arrange
        $user = User::factory()->create();
        $article = Article::factory()->create(['user_id' => $user->id]);
        $article->load('user');

        // Act
        $response = $this->responder->show($article);

        // Assert
        $data = $response->getData(true);
        $articleData = $data['data']['article'];
        
        // ISO8601形式の検証
        $this->assertRegExp('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/', $articleData['created_at']);
        $this->assertRegExp('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/', $articleData['updated_at']);
    }
}
```

**学習ポイント**:
- レスポンス構造の詳細な検証
- データ変換処理のテスト
- HTTPステータスコードの確認

## 🔗 結合テスト戦略

### Feature Test（ADR連携テスト）

#### テスト対象と観点
- HTTPエンドポイントの動作
- ADR3層の連携
- 認証・認可の動作
- エラーハンドリング

#### テスト実装例

```php
<?php
// tests/Feature/Articles/ArticleIndexTest.php

namespace Tests\Feature\Articles;

use App\Models\Article;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * 記事一覧機能テスト
 * 
 * 【学習ポイント】
 * - HTTPレベルでのADR連携テスト
 * - エンドツーエンドの動作確認
 * - 実際のHTTPリクエスト/レスポンス検証
 */
final class ArticleIndexTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function GET_api_articles_公開記事一覧を取得できる(): void
    {
        // Arrange
        $user = User::factory()->create();
        
        // 公開記事
        $publishedArticles = Article::factory()->count(3)->create([
            'user_id' => $user->id,
            'status' => 'published',
        ]);
        
        // 下書き記事（非表示）
        Article::factory()->count(2)->create([
            'user_id' => $user->id,
            'status' => 'draft',
        ]);

        // Act
        $response = $this->getJson('/api/articles');

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'articles' => [
                        '*' => [
                            'id',
                            'title',
                            'content',
                            'status',
                            'created_at',
                            'updated_at',
                            'author' => [
                                'id',
                                'name',
                            ],
                        ],
                    ],
                    'pagination' => [
                        'current_page',
                        'per_page',
                        'total',
                        'last_page',
                        'from',
                        'to',
                    ],
                ],
            ])
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'pagination' => [
                        'total' => 3, // 公開記事のみ
                    ],
                ],
            ]);

        // 公開記事のみが含まれていることを確認
        $articles = $response->json('data.articles');
        foreach ($articles as $article) {
            $this->assertEquals('published', $article['status']);
        }
    }

    /**
     * @test
     */
    public function GET_api_articles_ページネーションパラメータが機能する(): void
    {
        // Arrange
        $user = User::factory()->create();
        Article::factory()->count(25)->create([
            'user_id' => $user->id,
            'status' => 'published',
        ]);

        // Act
        $response = $this->getJson('/api/articles?page=2&per_page=5');

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'pagination' => [
                        'current_page' => 2,
                        'per_page' => 5,
                        'total' => 25,
                        'last_page' => 5,
                        'from' => 6,
                        'to' => 10,
                    ],
                ],
            ]);

        $this->assertCount(5, $response->json('data.articles'));
    }

    /**
     * @test
     */
    public function GET_api_articles_不正なページネーションパラメータでバリデーションエラー(): void
    {
        // Act & Assert
        $this->getJson('/api/articles?page=-1')
            ->assertStatus(422)
            ->assertJsonValidationErrors(['page']);

        $this->getJson('/api/articles?per_page=100')
            ->assertStatus(422)
            ->assertJsonValidationErrors(['per_page']);
    }

    /**
     * @test
     */
    public function GET_api_articles_記事が存在しない場合は空配列を返す(): void
    {
        // Act
        $response = $this->getJson('/api/articles');

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'articles' => [],
                    'pagination' => [
                        'total' => 0,
                    ],
                ],
            ]);
    }

    /**
     * @test
     */
    public function GET_api_articles_レスポンス時間が適切である(): void
    {
        // Arrange
        $user = User::factory()->create();
        Article::factory()->count(100)->create([
            'user_id' => $user->id,
            'status' => 'published',
        ]);

        // Act
        $startTime = microtime(true);
        $response = $this->getJson('/api/articles');
        $endTime = microtime(true);

        // Assert
        $response->assertStatus(200);
        
        $responseTime = ($endTime - $startTime) * 1000; // ミリ秒
        $this->assertLessThan(500, $responseTime, 'レスポンス時間が500ms以内であること');
    }
}
```

### 認証機能の結合テスト

```php
<?php
// tests/Feature/Auth/LoginTest.php

namespace Tests\Feature\Auth;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

/**
 * ログイン機能テスト
 * 
 * 【学習ポイント】
 * - 認証フローの結合テスト
 * - セキュリティ要件の検証
 * - エラーハンドリングのテスト
 */
final class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function POST_api_auth_login_正しい認証情報でログインできる(): void
    {
        // Arrange
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        // Act
        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'user' => [
                        'id',
                        'name',
                        'email',
                    ],
                ],
            ])
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'user' => [
                        'email' => 'test@example.com',
                    ],
                ],
            ]);

        // セッションに認証情報が保存されていることを確認
        $this->assertAuthenticatedAs($user);
    }

    /**
     * @test
     */
    public function POST_api_auth_login_間違ったパスワードでログインに失敗する(): void
    {
        // Arrange
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('correct-password'),
        ]);

        // Act
        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        // Assert
        $response->assertStatus(401)
            ->assertJson([
                'status' => 'error',
                'message' => '認証に失敗しました',
            ]);

        $this->assertGuest();
    }

    /**
     * @test
     */
    public function POST_api_auth_login_存在しないメールアドレスでログインに失敗する(): void
    {
        // Act
        $response = $this->postJson('/api/auth/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'password',
        ]);

        // Assert
        $response->assertStatus(401);
        $this->assertGuest();
    }

    /**
     * @test
     */
    public function POST_api_auth_login_バリデーションエラーが正しく返される(): void
    {
        // Act & Assert
        $this->postJson('/api/auth/login', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);

        $this->postJson('/api/auth/login', [
            'email' => 'invalid-email',
            'password' => '',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    /**
     * @test
     */
    public function POST_api_auth_login_レート制限が機能する(): void
    {
        // Arrange
        $user = User::factory()->create(['email' => 'test@example.com']);

        // Act: 短時間で大量のログイン試行
        for ($i = 0; $i < 6; $i++) {
            $this->postJson('/api/auth/login', [
                'email' => 'test@example.com',
                'password' => 'wrong-password',
            ]);
        }

        // Assert: レート制限でブロックされることを確認
        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(429); // Too Many Requests
    }
}
```

## 🎯 テスト実行・カバレッジ戦略

### テスト実行コマンド

```bash
# 全テスト実行
php artisan test

# 特定のテストスイート実行
php artisan test --testsuite=Unit
php artisan test --testsuite=Feature

# カバレッジレポート生成
php artisan test --coverage
php artisan test --coverage-html coverage-report

# 並列実行（高速化）
php artisan test --parallel

# 特定のテストクラス実行
php artisan test tests/Unit/Actions/Articles/IndexActionTest.php

# テスト結果の詳細表示
php artisan test --verbose
```

### カバレッジ目標

| 層 | 目標カバレッジ | 重点項目 |
|---|---------------|----------|
| **Action** | 95%以上 | 全メソッド、例外処理 |
| **UseCase** | 90%以上 | ビジネスロジック、エラーケース |
| **Responder** | 85%以上 | レスポンス形式、データ変換 |
| **全体** | 80%以上 | 重要な機能パス |

### テストデータ管理

#### ファクトリーの活用

```php
<?php
// database/factories/ArticleFactory.php

namespace Database\Factories;

use App\Models\User;
use App\Support\Enums\ArticleStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

final class ArticleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(),
            'content' => fake()->paragraphs(3, true),
            'status' => fake()->randomElement(ArticleStatus::cases()),
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ArticleStatus::Published,
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ArticleStatus::Draft,
        ]);
    }

    public function withUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }
}
```

## 🚀 テストのベストプラクティス

### 1. テスト命名規則

```php
// ❌ 悪い例
public function test_index(): void

// ✅ 良い例
public function 正常な記事一覧取得(): void
public function 権限のないユーザーは記事作成に失敗する(): void
public function 空の記事タイトルでバリデーションエラーが発生する(): void
```

### 2. AAA（Arrange-Act-Assert）パターン

```php
public function 記事作成が正常に動作する(): void
{
    // Arrange（準備）
    $user = User::factory()->create();
    $this->actingAs($user);
    
    $requestData = [
        'title' => 'Test Article',
        'content' => 'Test Content',
        'status' => 'published',
    ];

    // Act（実行）
    $response = $this->postJson('/api/admin/articles', $requestData);

    // Assert（検証）
    $response->assertStatus(201);
    $this->assertDatabaseHas('articles', [
        'title' => 'Test Article',
        'user_id' => $user->id,
    ]);
}
```

### 3. テストの独立性確保

```php
final class ArticleUseCaseTest extends TestCase
{
    use RefreshDatabase; // 各テストでDBをリセット

    protected function setUp(): void
    {
        parent::setUp();
        // 共通セットアップ
    }

    protected function tearDown(): void
    {
        // クリーンアップ処理
        parent::tearDown();
    }
}
```

### 4. モックの適切な使用

```php
// ✅ 外部依存をモック化
public function メール送信サービスが呼ばれることを確認(): void
{
    // Arrange
    $mockMailService = Mockery::mock(MailService::class);
    $mockMailService->shouldReceive('send')
        ->once()
        ->with(Mockery::type(User::class))
        ->andReturn(true);
    
    $this->app->instance(MailService::class, $mockMailService);

    // Act & Assert
    // ...
}
```

## 📝 継続的インテグレーション

### GitHub Actions設定例

```yaml
# .github/workflows/test.yml
name: Tests

on:
  push:
    branches: [ main, develop ]
  pull_request:
    branches: [ main ]

jobs:
  test:
    runs-on: ubuntu-latest

    steps:
    - uses: actions/checkout@v3

    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.3'
        extensions: dom, curl, libxml, mbstring, zip, pcntl, pdo, sqlite, pdo_sqlite

    - name: Cache dependencies
      uses: actions/cache@v3
      with:
        path: ~/.composer/cache/files
        key: dependencies-composer-${{ hashFiles('composer.json') }}

    - name: Install dependencies
      run: composer install --no-interaction --prefer-dist --optimize-autoloader

    - name: Prepare environment
      run: |
        cp .env.example .env
        php artisan key:generate

    - name: Create database
      run: |
        mkdir -p database
        touch database/database.sqlite

    - name: Run migrations
      run: php artisan migrate

    - name: Run tests
      run: php artisan test --coverage --min=80

    - name: Upload coverage reports
      uses: codecov/codecov-action@v3
```

## 🎓 学習チェックポイント

### テスト設計の理解
- [ ] 各層の責務に応じたテスト設計ができる
- [ ] 適切なテストダブル（モック/スタブ）を使い分けられる
- [ ] テストピラミッドを理解し、バランスの取れたテストを書ける

### ADRパターンでのテスト
- [ ] Action層のテストで依存関係を適切にモック化できる
- [ ] UseCase層でビジネスロジックを確実にテストできる
- [ ] Responder層でレスポンス形式を詳細に検証できる

### テスト品質の確保
- [ ] 可読性の高いテストコードを書ける
- [ ] テストの実行速度を考慮した設計ができる
- [ ] 適切なカバレッジ目標を設定し達成できる

### CI/CD連携
- [ ] 自動テスト実行環境を構築できる
- [ ] テスト結果の継続的な監視ができる
- [ ] テスト失敗時の適切な対応ができる

---

*このテスト戦略書を通じて、ADRパターンにおける効果的なテスト手法を習得し、高品質なLaravelアプリケーションの開発スキルを身につけることができます。* 