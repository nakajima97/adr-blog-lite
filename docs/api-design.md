# API設計書

## 概要

ADR Blog Lite のAPI設計書です。ADRパターン（Action-Domain-Responder）の学習に最適化された設計となっています。

## 設計方針

### ADRパターンでのAPI設計の特徴
- **Action**: エントリポイントとしてHTTPリクエストを受け取る
- **UseCase**: ビジネスロジックを実行
- **Responder**: 統一されたレスポンス形式で返却

### RESTful設計原則
- リソース指向のURL設計
- 適切なHTTPメソッドの使用
- ステータスコードの統一
- JSON形式でのデータ交換

## 認証方式

### Laravel Sanctum (SPA認証)
```
POST /api/auth/login
- セッションベース認証
- CSRF保護
- Cookie自動設定
```

## エンドポイント一覧

### 認証関連API

#### ログイン
```
POST /api/auth/login
```

**Action**: `App\Actions\Auth\LoginAction`
**UseCase**: `App\UseCases\Auth\LoginUseCase`
**Responder**: `App\Responders\Api\AuthResponder`

**リクエスト**:
```json
{
  "email": "admin@example.com",
  "password": "password"
}
```

**レスポンス（成功）**:
```json
{
  "status": "success",
  "message": "ログインしました",
  "data": {
    "user": {
      "id": 1,
      "name": "Blog Admin",
      "email": "admin@example.com"
    }
  }
}
```

**レスポンス（失敗）**:
```json
{
  "status": "error",
  "message": "認証に失敗しました",
  "errors": {
    "email": ["メールアドレスまたはパスワードが正しくありません"]
  }
}
```

#### ログアウト
```
POST /api/auth/logout
```

**レスポンス**:
```json
{
  "status": "success",
  "message": "ログアウトしました"
}
```

#### 認証確認
```
GET /api/auth/user
```

**レスポンス**:
```json
{
  "status": "success",
  "data": {
    "user": {
      "id": 1,
      "name": "Blog Admin",
      "email": "admin@example.com"
    }
  }
}
```

### 記事関連API（公開）

#### 記事一覧取得
```
GET /api/articles
```

**Action**: `App\Actions\Articles\IndexAction`
**UseCase**: `App\UseCases\Articles\GetPublishedArticlesUseCase`
**Responder**: `App\Responders\Api\ArticleResponder`

**クエリパラメータ**:
- `page`: ページ番号（デフォルト: 1）
- `per_page`: 1ページあたりの件数（デフォルト: 10、最大: 50）

**レスポンス**:
```json
{
  "status": "success",
  "data": {
    "articles": [
      {
        "id": 1,
        "title": "サンプル記事タイトル",
        "content": "記事の内容...",
        "status": "published",
        "created_at": "2024-01-01T12:00:00Z",
        "updated_at": "2024-01-01T12:00:00Z",
        "author": {
          "id": 1,
          "name": "Blog Admin"
        }
      }
    ],
    "pagination": {
      "current_page": 1,
      "per_page": 10,
      "total": 25,
      "last_page": 3,
      "from": 1,
      "to": 10
    }
  }
}
```

#### 記事詳細取得
```
GET /api/articles/{id}
```

**Action**: `App\Actions\Articles\ShowAction`
**UseCase**: `App\UseCases\Articles\GetArticleUseCase`
**Responder**: `App\Responders\Api\ArticleResponder`

**レスポンス（成功）**:
```json
{
  "status": "success",
  "data": {
    "article": {
      "id": 1,
      "title": "サンプル記事タイトル",
      "content": "# マークダウンで書かれた記事内容\n\nここに記事の本文が入ります。",
      "content_html": "<h1>マークダウンで書かれた記事内容</h1>\n<p>ここに記事の本文が入ります。</p>",
      "status": "published",
      "created_at": "2024-01-01T12:00:00Z",
      "updated_at": "2024-01-01T12:00:00Z",
      "author": {
        "id": 1,
        "name": "Blog Admin"
      }
    },
    "navigation": {
      "previous": {
        "id": 2,
        "title": "前の記事タイトル"
      },
      "next": null
    }
  }
}
```

**レスポンス（記事が見つからない）**:
```json
{
  "status": "error",
  "message": "記事が見つかりません",
  "code": "ARTICLE_NOT_FOUND"
}
```

### 記事関連API（管理者）

#### 管理者用記事一覧取得
```
GET /api/admin/articles
```

**Action**: `App\Actions\Admin\Articles\IndexAction`
**UseCase**: `App\UseCases\Admin\Articles\GetAllArticlesUseCase`
**Responder**: `App\Responders\Api\AdminResponder`

**認証**: 必須

**クエリパラメータ**:
- `page`: ページ番号
- `per_page`: 1ページあたりの件数
- `status`: フィルタ（`draft`, `published`, `all`）

**レスポンス**:
```json
{
  "status": "success",
  "data": {
    "articles": [
      {
        "id": 1,
        "title": "記事タイトル",
        "content": "記事内容の冒頭部分...",
        "status": "published",
        "created_at": "2024-01-01T12:00:00Z",
        "updated_at": "2024-01-01T12:00:00Z",
        "actions": {
          "edit": "/api/admin/articles/1",
          "delete": "/api/admin/articles/1"
        }
      }
    ],
    "pagination": {
      "current_page": 1,
      "per_page": 10,
      "total": 25,
      "last_page": 3
    }
  }
}
```

#### 記事作成
```
POST /api/admin/articles
```

**Action**: `App\Actions\Admin\Articles\CreateAction`
**UseCase**: `App\UseCases\Admin\Articles\CreateArticleUseCase`
**Responder**: `App\Responders\Api\AdminResponder`

**認証**: 必須

**リクエスト**:
```json
{
  "title": "新しい記事のタイトル",
  "content": "# 新しい記事\n\nマークダウンで書かれた内容です。",
  "status": "draft"
}
```

**レスポンス（成功）**:
```json
{
  "status": "success",
  "message": "記事を作成しました",
  "data": {
    "article": {
      "id": 26,
      "title": "新しい記事のタイトル",
      "content": "# 新しい記事\n\nマークダウンで書かれた内容です。",
      "status": "draft",
      "created_at": "2024-01-01T15:00:00Z",
      "updated_at": "2024-01-01T15:00:00Z"
    }
  }
}
```

**レスポンス（バリデーションエラー）**:
```json
{
  "status": "error",
  "message": "入力内容に誤りがあります",
  "errors": {
    "title": ["タイトルは必須です"],
    "content": ["本文は必須です"]
  }
}
```

#### 記事更新
```
PUT /api/admin/articles/{id}
```

**Action**: `App\Actions\Admin\Articles\UpdateAction`
**UseCase**: `App\UseCases\Admin\Articles\UpdateArticleUseCase`
**Responder**: `App\Responders\Api\AdminResponder`

**リクエスト**:
```json
{
  "title": "更新されたタイトル",
  "content": "更新された本文です。",
  "status": "published"
}
```

**レスポンス**:
```json
{
  "status": "success",
  "message": "記事を更新しました",
  "data": {
    "article": {
      "id": 1,
      "title": "更新されたタイトル",
      "content": "更新された本文です。",
      "status": "published",
      "created_at": "2024-01-01T12:00:00Z",
      "updated_at": "2024-01-01T15:30:00Z"
    }
  }
}
```

#### 記事削除
```
DELETE /api/admin/articles/{id}
```

**Action**: `App\Actions\Admin\Articles\DeleteAction`
**UseCase**: `App\UseCases\Admin\Articles\DeleteArticleUseCase`
**Responder**: `App\Responders\Api\AdminResponder`

**レスポンス**:
```json
{
  "status": "success",
  "message": "記事を削除しました"
}
```

## レスポンス仕様

### 標準レスポンス構造

#### 成功レスポンス
```json
{
  "status": "success",
  "message": "処理が完了しました",
  "data": {
    // 実際のデータ
  }
}
```

#### エラーレスポンス
```json
{
  "status": "error",
  "message": "エラーメッセージ",
  "code": "ERROR_CODE",
  "errors": {
    "field": ["具体的なエラー内容"]
  }
}
```

### HTTPステータスコード

| コード | 意味 | 使用場面 |
|--------|------|----------|
| 200 | OK | 正常な取得・更新 |
| 201 | Created | 正常な作成 |
| 400 | Bad Request | バリデーションエラー |
| 401 | Unauthorized | 認証エラー |
| 403 | Forbidden | 認可エラー |
| 404 | Not Found | リソースが見つからない |
| 422 | Unprocessable Entity | バリデーションエラー（詳細） |
| 500 | Internal Server Error | サーバーエラー |

## ADRパターンでのAPI実装例

### Action層の実装例
```php
<?php
// app/Actions/Articles/IndexAction.php

namespace App\Actions\Articles;

use App\Http\Requests\Articles\IndexRequest;
use App\UseCases\Articles\GetPublishedArticlesUseCase;
use App\Responders\Api\ArticleResponder;
use Illuminate\Http\JsonResponse;

final readonly class IndexAction
{
    public function __construct(
        private GetPublishedArticlesUseCase $useCase,
        private ArticleResponder $responder,
    ) {}

    public function __invoke(IndexRequest $request): JsonResponse
    {
        $articles = $this->useCase->execute(
            page: $request->integer('page', 1),
            perPage: $request->integer('per_page', 10)
        );

        return $this->responder->index($articles);
    }
}
```

### UseCase層の実装例
```php
<?php
// app/UseCases/Articles/GetPublishedArticlesUseCase.php

namespace App\UseCases\Articles;

use App\Models\Article;
use Illuminate\Pagination\LengthAwarePaginator;

final readonly class GetPublishedArticlesUseCase
{
    public function execute(int $page = 1, int $perPage = 10): LengthAwarePaginator
    {
        return Article::published()
            ->with('user')
            ->latest()
            ->paginate($perPage, ['*'], 'page', $page);
    }
}
```

### Responder層の実装例
```php
<?php
// app/Responders/Api/ArticleResponder.php

namespace App\Responders\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

final readonly class ArticleResponder
{
    public function index(LengthAwarePaginator $articles): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'articles' => $articles->items(),
                'pagination' => [
                    'current_page' => $articles->currentPage(),
                    'per_page' => $articles->perPage(),
                    'total' => $articles->total(),
                    'last_page' => $articles->lastPage(),
                    'from' => $articles->firstItem(),
                    'to' => $articles->lastItem(),
                ],
            ],
        ]);
    }

    public function show(Article $article): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'article' => $article->load('user'),
            ],
        ]);
    }

    public function created(Article $article): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => '記事を作成しました',
            'data' => [
                'article' => $article,
            ],
        ], 201);
    }
}
```

## バリデーション仕様

### 記事作成・更新リクエスト
```php
<?php
// app/Http/Requests/Admin/Articles/CreateRequest.php

namespace App\Http\Requests\Admin\Articles;

use Illuminate\Foundation\Http\FormRequest;

final class CreateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:10000'],
            'status' => ['required', 'in:draft,published'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'タイトルは必須です',
            'title.max' => 'タイトルは255文字以内で入力してください',
            'content.required' => '本文は必須です',
            'content.max' => '本文は10,000文字以内で入力してください',
            'status.in' => '公開状態は「draft」または「published」を指定してください',
        ];
    }
}
```

## エラーハンドリング

### カスタム例外
```php
<?php
// app/Exceptions/ArticleNotFoundException.php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

final class ArticleNotFoundException extends Exception
{
    public function render(): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => '記事が見つかりません',
            'code' => 'ARTICLE_NOT_FOUND',
        ], 404);
    }
}
```

## ルーティング設計

### routes/api.php
```php
<?php

use App\Actions\Articles\IndexAction;
use App\Actions\Articles\ShowAction;
use App\Actions\Auth\LoginAction;
use App\Actions\Auth\LogoutAction;
use App\Actions\Admin\Articles\CreateAction;

// 公開API
Route::prefix('articles')->group(function () {
    Route::get('/', IndexAction::class);
    Route::get('/{article}', ShowAction::class);
});

// 認証API
Route::prefix('auth')->group(function () {
    Route::post('/login', LoginAction::class);
    Route::post('/logout', LogoutAction::class)->middleware('auth:sanctum');
    Route::get('/user', [AuthController::class, 'user'])->middleware('auth:sanctum');
});

// 管理者API
Route::prefix('admin')->middleware('auth:sanctum')->group(function () {
    Route::apiResource('articles', ArticleController::class);
});
```

## 学習ポイント

### ADRパターンでのAPI設計
1. **Action**: 単一のエンドポイントに対する責務
2. **UseCase**: ビジネスロジックの実装
3. **Responder**: レスポンス形式の統一

### レスポンス設計のベストプラクティス
1. **一貫性**: 全エンドポイントで統一されたレスポンス構造
2. **予測可能性**: クライアントが期待するデータ構造
3. **エラーハンドリング**: 適切なHTTPステータスコードとエラーメッセージ

### パフォーマンス考慮
1. **ページネーション**: 大量データの効率的な取得
2. **N+1問題の回避**: Eloquentの `with()` 使用
3. **適切なインデックス**: データベースクエリの最適化

---

*このAPI設計は、ADRパターンの学習に最適化されており、実際の本格的なAPI開発では追加のセキュリティ要件やパフォーマンス最適化が必要です。* 