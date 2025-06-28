<?php

namespace App\UseCases\Article;

use App\Models\Article;
use Illuminate\Support\Facades\DB;

/**
 * 記事作成UseCase
 *
 * ADRパターンにおけるドメイン層:
 * - 記事作成のビジネスロジック
 * - トランザクション管理
 * - データ整合性の保証
 */
final readonly class CreateArticleUseCase
{
    /**
     * 記事作成実行
     *
     * @param  array  $data  記事作成データ（title, content, status, user_id）
     * @return Article 作成された記事
     *
     * @throws \Throwable
     */
    public function execute(array $data): Article
    {
        return DB::transaction(function () use ($data): Article {
            // 記事データの作成
            $article = Article::create([
                'title' => $data['title'],
                'content' => $data['content'],
                'status' => $data['status'] ?? 'draft',
                'user_id' => $data['user_id'],
            ]);

            // 作成された記事を投稿者情報と共に返す
            return $article->load('user');
        });
    }

    /**
     * タイトル重複チェック
     *
     * @param  string  $title  チェック対象のタイトル
     * @return bool 重複している場合true
     */
    public function isDuplicateTitle(string $title): bool
    {
        return Article::where('title', $title)->exists();
    }

    /**
     * 作成可能な記事データの検証
     *
     * @param  array  $data  検証対象データ
     * @return bool 作成可能な場合true
     */
    public function canCreate(array $data): bool
    {
        // 必須フィールドの存在確認
        $requiredFields = ['title', 'content', 'user_id'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                return false;
            }
        }

        // タイトル重複チェック
        if ($this->isDuplicateTitle($data['title'])) {
            return false;
        }

        // ステータス値チェック
        if (isset($data['status']) && ! in_array($data['status'], ['draft', 'published'])) {
            return false;
        }

        return true;
    }
}
