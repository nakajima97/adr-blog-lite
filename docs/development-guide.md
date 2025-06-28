# 開発手順書（ADRパターン学習重点）

## 概要

ADR Blog Lite の開発手順書です。ADRパターン（Action-Domain-Responder）の学習に重点を置いた実装ガイドとなっています。

## 🎯 学習目標

### ADRパターンの理解
1. **Action**: HTTPリクエストの受け取りとレスポンス制御
2. **Domain (UseCase)**: ビジネスロジックの実装
3. **Responder**: レスポンス形式の統一

### 実装スキルの習得
1. Laravel 12 の最新機能活用
2. 型安全なコードの実装
3. テスト駆動開発の実践
4. Eloquent ORM の効果的な使用

## 📁 ディレクトリ構造セットアップ

### 1. 基本ディレクトリ作成

```bash
# ADRパターン用ディレクトリ作成
mkdir -p app/{Actions,UseCases,Responders/{Api,Web},Support/{Enums,Traits},Exceptions}

# サブディレクトリ作成
mkdir -p app/Actions/{Articles,Auth,Admin/Articles}
mkdir -p app/UseCases/{Articles,Auth,Admin/Articles}
mkdir -p app/Responders/Api
mkdir -p app/Responders/Web
```

### 2. ディレクトリ構造確認

```
app/
├── Actions/              # ADRのAction層
│   ├── Articles/         # 公開記事関連
│   ├── Auth/            # 認証関連  
│   └── Admin/           # 管理機能
│       └── Articles/    # 管理記事関連
├── UseCases/            # ビジネスロジック
│   ├── Articles/
│   ├── Auth/
│   └── Admin/
│       └── Articles/
├── Responders/          # レスポンス整形
│   ├── Api/            # JSON API用
│   └── Web/            # Web画面用
├── Models/             # Eloquentモデル
├── Http/               # Laravel標準（最小限）
│   └── Requests/       # フォームリクエスト
├── Support/            # ヘルパー・ユーティリティ
│   ├── Enums/          # 列挙型
│   └── Traits/         # トレイト
└── Exceptions/         # カスタム例外
```

## 🏗️ ADRパターン実装ガイド

### Phase 1: 基盤クラス作成

#### 1.1 基底Actionクラス

```php
<?php
// app/Actions/BaseAction.php

namespace App\Actions;

abstract class BaseAction
{
    /**
     * Action実行の共通処理
     */
    protected function beforeExecute(): void
    {
        // ログ出力、権限チェックなど
    }

    protected function afterExecute(): void
    {
        // 後処理、ログ出力など
    }
}
```

#### 1.2 基底UseCaseクラス

```php
<?php
// app/UseCases/BaseUseCase.php

namespace App\UseCases;

abstract readonly class BaseUseCase
{
    /**
     * UseCase実行時の共通ログ
     */
    protected function logExecution(string $operation, array $params = []): void
    {
        logger()->info("UseCase executed: {$operation}", $params);
    }
}
```

#### 1.3 基底Responderクラス

```php
<?php
// app/Responders/BaseResponder.php

namespace App\Responders;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

abstract readonly class BaseResponder
{
    /**
     * 成功レスポンスの統一フォーマット
     */
    protected function successResponse(
        string $message, 
        mixed $data = null, 
        int $status = 200
    ): JsonResponse {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    /**
     * エラーレスポンスの統一フォーマット
     */
    protected function errorResponse(
        string $message, 
        mixed $errors = null, 
        int $status = 400
    ): JsonResponse {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'errors' => $errors,
        ], $status);
    }
}
```

### Phase 2: 記事一覧機能実装（学習例）

#### 2.1 Action層の実装

```php
<?php
// app/Actions/Articles/IndexAction.php

namespace App\Actions\Articles;

use App\Actions\BaseAction;
use App\Http\Requests\Articles\IndexRequest;
use App\UseCases\Articles\GetPublishedArticlesUseCase;
use App\Responders\Api\ArticleResponder;
use Illuminate\Http\JsonResponse;

/**
 * 記事一覧取得Action
 * 
 * 【学習ポイント】
 * - HTTPリクエストの受け取り
 * - UseCaseへの適切な委譲
 * - Responderでのレスポンス統一
 */
final readonly class IndexAction extends BaseAction
{
    public function __construct(
        private GetPublishedArticlesUseCase $useCase,
        private ArticleResponder $responder,
    ) {}

    public function __invoke(IndexRequest $request): JsonResponse
    {
        // バリデーション済みデータの取得
        $page = $request->integer('page', 1);
        $perPage = $request->integer('per_page', 10);

        // UseCaseでビジネスロジック実行
        $articles = $this->useCase->execute($page, $perPage);

        // Responderでレスポンス整形
        return $this->responder->index($articles);
    }
}
```

**学習ポイント**:
- Actionは薄く、ビジネスロジックを含まない
- 依存性注入でUseCase・Responderを受け取る
- readonly修飾子で不変性を保証

#### 2.2 UseCase層の実装

```php
<?php
// app/UseCases/Articles/GetPublishedArticlesUseCase.php

namespace App\UseCases\Articles;

use App\UseCases\BaseUseCase;
use App\Models\Article;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * 公開記事一覧取得UseCase
 * 
 * 【学習ポイント】
 * - ビジネスロジックの実装
 * - Eloquentの効果的な使用
 * - N+1問題の回避
 */
final readonly class GetPublishedArticlesUseCase extends BaseUseCase
{
    public function execute(int $page = 1, int $perPage = 10): LengthAwarePaginator
    {
        $this->logExecution('GetPublishedArticles', compact('page', 'perPage'));

        return Article::published()          // スコープ使用
            ->with('user')                   // N+1問題回避
            ->latest()                       // 最新順
            ->paginate($perPage, ['*'], 'page', $page);
    }
}
```

**学習ポイント**:
- ビジネスロジックの中心となる処理
- Eloquentスコープの活用
- パフォーマンスを考慮したクエリ

#### 2.3 Responder層の実装

```php
<?php
// app/Responders/Api/ArticleResponder.php

namespace App\Responders\Api;

use App\Responders\BaseResponder;
use App\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * 記事関連レスポンダー
 * 
 * 【学習ポイント】
 * - レスポンス形式の統一
 * - データ変換・整形
 * - 適切なHTTPステータスコード
 */
final readonly class ArticleResponder extends BaseResponder
{
    public function index(LengthAwarePaginator $articles): JsonResponse
    {
        return $this->successResponse(
            message: '記事一覧を取得しました',
            data: [
                'articles' => $articles->through(fn ($article) => [
                    'id' => $article->id,
                    'title' => $article->title,
                    'content' => $this->truncateContent($article->content),
                    'status' => $article->status,
                    'created_at' => $article->created_at->toISOString(),
                    'updated_at' => $article->updated_at->toISOString(),
                    'author' => [
                        'id' => $article->user->id,
                        'name' => $article->user->name,
                    ],
                ]),
                'pagination' => [
                    'current_page' => $articles->currentPage(),
                    'per_page' => $articles->perPage(),
                    'total' => $articles->total(),
                    'last_page' => $articles->lastPage(),
                    'from' => $articles->firstItem(),
                    'to' => $articles->lastItem(),
                ],
            ]
        );
    }

    public function show(Article $article): JsonResponse
    {
        return $this->successResponse(
            message: '記事詳細を取得しました',
            data: [
                'article' => [
                    'id' => $article->id,
                    'title' => $article->title,
                    'content' => $article->content,
                    'content_html' => \Illuminate\Support\Str::markdown($article->content),
                    'status' => $article->status,
                    'created_at' => $article->created_at->toISOString(),
                    'updated_at' => $article->updated_at->toISOString(),
                    'author' => [
                        'id' => $article->user->id,
                        'name' => $article->user->name,
                    ],
                ],
            ]
        );
    }

    public function created(Article $article): JsonResponse
    {
        return $this->successResponse(
            message: '記事を作成しました',
            data: ['article' => $article],
            status: 201
        );
    }

    /**
     * コンテンツの切り詰め処理
     */
    private function truncateContent(string $content, int $length = 200): string
    {
        return \Illuminate\Support\Str::limit(strip_tags($content), $length);
    }
}
```

**学習ポイント**:
- データ変換ロジックの実装
- 一貫したレスポンス形式
- プライベートメソッドでの補助処理

### Phase 3: フォームリクエスト実装

#### 3.1 記事一覧リクエスト

```php
<?php
// app/Http/Requests/Articles/IndexRequest.php

namespace App\Http\Requests\Articles;

use Illuminate\Foundation\Http\FormRequest;

/**
 * 記事一覧取得リクエスト
 * 
 * 【学習ポイント】
 * - クエリパラメータのバリデーション
 * - デフォルト値の設定
 * - 型安全な値の取得
 */
final class IndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // 公開API
    }

    public function rules(): array
    {
        return [
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'page.integer' => 'ページ番号は整数で指定してください',
            'page.min' => 'ページ番号は1以上で指定してください',
            'per_page.integer' => '1ページあたりの件数は整数で指定してください',
            'per_page.min' => '1ページあたりの件数は1以上で指定してください',
            'per_page.max' => '1ページあたりの件数は50以下で指定してください',
        ];
    }
}
```

#### 3.2 記事作成リクエスト

```php
<?php
// app/Http/Requests/Admin/Articles/CreateRequest.php

namespace App\Http\Requests\Admin\Articles;

use App\Support\Enums\ArticleStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * 記事作成リクエスト
 * 
 * 【学習ポイント】
 * - 複雑なバリデーションルール
 * - 列挙型の使用
 * - 認可処理の実装
 */
final class CreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check(); // 認証必須
    }

    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'max:255',
                'min:1',
            ],
            'content' => [
                'required',
                'string',
                'max:10000',
                'min:10',
            ],
            'status' => [
                'required',
                Rule::enum(ArticleStatus::class),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'タイトルは必須です',
            'title.max' => 'タイトルは255文字以内で入力してください',
            'title.min' => 'タイトルは1文字以上で入力してください',
            'content.required' => '本文は必須です',
            'content.max' => '本文は10,000文字以内で入力してください',
            'content.min' => '本文は10文字以上で入力してください',
            'status.required' => '公開状態は必須です',
        ];
    }

    /**
     * バリデーション後の処理
     */
    protected function passedValidation(): void
    {
        // ユーザーIDを自動設定
        $this->merge([
            'user_id' => auth()->id(),
        ]);
    }
}
```

### Phase 4: 列挙型（Enum）の活用

#### 4.1 記事ステータス定義

```php
<?php
// app/Support/Enums/ArticleStatus.php

namespace App\Support\Enums;

/**
 * 記事公開状態
 * 
 * 【学習ポイント】
 * - PHP 8.1+ の列挙型活用
 * - 型安全性の向上
 * - ビジネスロジックの集約
 */
enum ArticleStatus: string
{
    case Draft = 'draft';
    case Published = 'published';

    /**
     * 表示名取得
     */
    public function label(): string
    {
        return match ($this) {
            self::Draft => '下書き',
            self::Published => '公開',
        };
    }

    /**
     * 公開中かどうか
     */
    public function isPublished(): bool
    {
        return $this === self::Published;
    }

    /**
     * CSS クラス取得
     */
    public function cssClass(): string
    {
        return match ($this) {
            self::Draft => 'bg-gray-100 text-gray-800',
            self::Published => 'bg-green-100 text-green-800',
        };
    }

    /**
     * 全ステータスの選択肢配列
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($status) => [$status->value => $status->label()])
            ->toArray();
    }
}
```

### Phase 5: 複雑なビジネスロジック実装例

#### 5.1 記事作成UseCase

```php
<?php
// app/UseCases/Admin/Articles/CreateArticleUseCase.php

namespace App\UseCases\Admin\Articles;

use App\UseCases\BaseUseCase;
use App\Models\Article;
use App\Support\Enums\ArticleStatus;
use Illuminate\Support\Facades\DB;

/**
 * 記事作成UseCase
 * 
 * 【学習ポイント】
 * - トランザクション処理
 * - 複雑なビジネスロジック
 * - エラーハンドリング
 */
final readonly class CreateArticleUseCase extends BaseUseCase
{
    public function execute(
        int $userId,
        string $title,
        string $content,
        ArticleStatus $status
    ): Article {
        $this->logExecution('CreateArticle', [
            'user_id' => $userId,
            'title' => $title,
            'status' => $status->value,
        ]);

        return DB::transaction(function () use ($userId, $title, $content, $status) {
            // 重複チェック
            $this->checkDuplicateTitle($userId, $title);

            // 記事作成
            $article = Article::create([
                'user_id' => $userId,
                'title' => $title,
                'content' => $content,
                'status' => $status,
            ]);

            // 公開時の追加処理
            if ($status->isPublished()) {
                $this->handlePublishingProcess($article);
            }

            return $article->fresh(); // 最新データで返却
        });
    }

    /**
     * タイトル重複チェック
     */
    private function checkDuplicateTitle(int $userId, string $title): void
    {
        $exists = Article::where('user_id', $userId)
            ->where('title', $title)
            ->exists();

        if ($exists) {
            throw new \App\Exceptions\DuplicateTitleException(
                '同じタイトルの記事が既に存在します'
            );
        }
    }

    /**
     * 公開時の処理
     */
    private function handlePublishingProcess(Article $article): void
    {
        // 公開時のビジネスロジック
        // - 通知処理
        // - キャッシュクリア
        // - 検索インデックス更新など

        logger()->info('Article published', ['article_id' => $article->id]);
    }
}
```

#### 5.2 カスタム例外

```php
<?php
// app/Exceptions/DuplicateTitleException.php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

/**
 * タイトル重複例外
 * 
 * 【学習ポイント】
 * - カスタム例外の実装
 * - 適切なHTTPレスポンス
 * - エラーメッセージの統一
 */
final class DuplicateTitleException extends Exception
{
    public function render(): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $this->getMessage(),
            'code' => 'DUPLICATE_TITLE',
        ], 422);
    }
}
```

### Phase 6: 認証関連の実装

#### 6.1 ログインAction

```php
<?php
// app/Actions/Auth/LoginAction.php

namespace App\Actions\Auth;

use App\Actions\BaseAction;
use App\Http\Requests\Auth\LoginRequest;
use App\UseCases\Auth\LoginUseCase;
use App\Responders\Api\AuthResponder;
use Illuminate\Http\JsonResponse;

/**
 * ログインAction
 * 
 * 【学習ポイント】
 * - 認証処理の実装
 * - セキュリティの考慮
 * - エラーハンドリング
 */
final readonly class LoginAction extends BaseAction
{
    public function __construct(
        private LoginUseCase $useCase,
        private AuthResponder $responder,
    ) {}

    public function __invoke(LoginRequest $request): JsonResponse
    {
        try {
            $user = $this->useCase->execute(
                email: $request->string('email'),
                password: $request->string('password'),
                remember: $request->boolean('remember', false)
            );

            return $this->responder->loggedIn($user);

        } catch (\App\Exceptions\AuthenticationFailedException $e) {
            return $this->responder->authenticationFailed();
        }
    }
}
```

#### 6.2 ログインUseCase

```php
<?php
// app/UseCases/Auth/LoginUseCase.php

namespace App\UseCases\Auth;

use App\UseCases\BaseUseCase;
use App\Models\User;
use App\Exceptions\AuthenticationFailedException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * ログインUseCase
 * 
 * 【学習ポイント】
 * - 認証ロジックの実装
 * - セキュリティの考慮
 * - ログ記録
 */
final readonly class LoginUseCase extends BaseUseCase
{
    public function execute(string $email, string $password, bool $remember = false): User
    {
        $this->logExecution('Login', ['email' => $email]);

        // ユーザー検索
        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            // セキュリティ：同じ時間でレスポンス
            usleep(random_int(100000, 300000));
            
            throw new AuthenticationFailedException('認証に失敗しました');
        }

        // ログイン処理
        Auth::login($user, $remember);

        logger()->info('User logged in', ['user_id' => $user->id]);

        return $user;
    }
}
```

## 🛠️ 開発手順（段階的実装）

### Step 1: 環境セットアップ
```bash
# 1. 依存関係のインストール
composer install

# 2. 環境ファイル設定
cp .env.example .env
php artisan key:generate

# 3. データベース設定（SQLite）
touch database/database.sqlite
php artisan migrate

# 4. シーダー実行
php artisan db:seed
```

### Step 2: 基盤クラス作成
1. BaseAction, BaseUseCase, BaseResponder作成
2. カスタム例外クラス作成
3. Enumクラス作成

### Step 3: 機能別実装（推奨順序）
1. **記事一覧表示**（基本的なADRパターン）
2. **記事詳細表示**（URL パラメータ処理）
3. **管理者ログイン**（認証処理）
4. **記事作成**（フォーム処理・トランザクション）
5. **記事更新・削除**（複雑なビジネスロジック）

### Step 4: テスト実装
各機能の実装後、対応するテストを作成

### Step 5: リファクタリング
実装完了後、コードの改善・最適化

## 📚 学習の進め方

### 1週目: ADRパターンの基礎
- Action, UseCase, Responderの役割理解
- 記事一覧・詳細機能の実装
- 基本的なテストの作成

### 2週目: フォーム処理とバリデーション
- FormRequestの実装
- 記事作成・更新機能
- バリデーションテスト

### 3週目: 認証とセキュリティ
- 認証機能の実装
- セキュリティ対策
- 認証テスト

### 4週目: 高度な実装とテスト
- 複雑なビジネスロジック
- エラーハンドリング
- 結合テスト

## 🎓 チェックポイント

### ADRパターンの理解度確認
- [ ] ActionとControllerの違いを説明できる
- [ ] UseCaseでのビジネスロジック実装ができる
- [ ] Responderでレスポンス統一ができる
- [ ] 各層の責務を適切に分離できる

### Laravel スキル確認
- [ ] Eloquent ORM を効果的に使用できる
- [ ] FormRequest でバリデーションを実装できる
- [ ] 認証機能を実装できる
- [ ] テストを適切に作成できる

### コード品質確認
- [ ] 型安全なコードを書けている
- [ ] 適切なエラーハンドリングができている
- [ ] 保守性の高いコード構造になっている
- [ ] テストカバレッジが十分である

---

*この開発手順書を通じて、ADRパターンの実践的なスキルを身につけ、保守性の高いLaravelアプリケーションの開発手法を習得できます。* 