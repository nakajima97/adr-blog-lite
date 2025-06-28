<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * 記事作成リクエスト
 *
 * ADRパターンにおけるAction層で使用:
 * - フォームデータのバリデーション
 * - CSRF保護
 * - エラーメッセージの管理
 */
class CreateArticleRequest extends FormRequest
{
    /**
     * リクエストの認可判定
     *
     * 現在は認証機能がないため、常にtrueを返す
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * バリデーションルール
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'max:255',
                'unique:articles,title', // 同一タイトルの重複チェック
            ],
            'content' => [
                'required',
                'string',
                'min:10',
            ],
            'status' => [
                'nullable',
                'in:draft,published',
            ],
            // オプションフィールド（既存フォームに存在するため）
            'category' => [
                'nullable',
                'string',
                'in:technology,design,business,lifestyle,other',
            ],
            'tags' => [
                'nullable',
                'string',
                'max:500',
            ],
        ];
    }

    /**
     * カスタムバリデーションメッセージ
     */
    public function messages(): array
    {
        return [
            'title.required' => 'タイトルは必須です。',
            'title.max' => 'タイトルは255文字以内で入力してください。',
            'title.unique' => 'このタイトルは既に使用されています。別のタイトルを入力してください。',
            'content.required' => '記事内容は必須です。',
            'content.min' => '記事内容は10文字以上で入力してください。',
            'status.in' => '公開状態は「下書き」または「公開」のいずれかを選択してください。',
            'category.in' => '有効なカテゴリーを選択してください。',
            'tags.max' => 'タグは500文字以内で入力してください。',
        ];
    }

    /**
     * バリデーション後の処理
     *
     * デフォルト値の設定と不要なフィールドの除去
     */
    protected function prepareForValidation(): void
    {
        // ステータスのデフォルト値設定
        if (! $this->has('status') || $this->status === null) {
            $this->merge(['status' => 'draft']);
        }
    }

    /**
     * バリデート済みデータから記事作成用データを抽出
     *
     * 核となるフィールド（title, content, status）のみを返す
     */
    public function getArticleData(): array
    {
        $validated = $this->validated();

        return [
            'title' => $validated['title'],
            'content' => $validated['content'],
            'status' => $validated['status'] ?? 'draft',
            'user_id' => 1, // 固定値（認証機能なしのため）
        ];
    }
}
