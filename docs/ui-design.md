# UI設計書

## 概要

ADR Blog Lite の画面設計書です。学習目的に最適化されたシンプルで実用的なUI設計となっています。

## 設計方針

### 学習重視の設計
- **シンプルな構成**: 複雑なUIを避け、ADRパターンの学習に集中
- **レスポンシブ対応**: モバイル・デスクトップの両方に対応
- **アクセシビリティ**: 基本的なWebアクセシビリティに配慮
- **Laravel標準**: Blade テンプレートとTailwind CSS使用

### 技術スタック
- **フロントエンド**: Blade テンプレート
- **CSS フレームワーク**: Tailwind CSS（学習用）
- **JavaScript**: 最小限のバニラJS（必要時のみ）
- **アイコン**: Heroicons（無料・Tailwind CSS互換）

**注記**: Livewire、Alpine.jsは学習焦点の明確化のため使用しません。

## 画面一覧

### 公開画面
1. 記事一覧画面 (`/`)
2. 記事詳細画面 (`/articles/{id}`)

### 管理画面
3. 記事管理一覧画面 (`/articles/manage`)
4. 記事投稿・編集画面 (`/articles/create`, `/articles/{id}/edit`)

**注記**: 認証機能は学習範囲外のため、ログイン画面は作成しません。

## ワイヤーフレーム

### 1. 記事一覧画面（公開）

```
┌─────────────────────────────────────────────────────────┐
│ [Blog Title]                           [検索ボックス]    │
├─────────────────────────────────────────────────────────┤
│                                                         │
│ ┌─────────────────────────────────────────────────────┐ │
│ │ 記事タイトル 1                                      │ │
│ │ 投稿日: 2024-01-01                                  │ │
│ │ 記事の要約が表示されます...                         │ │
│ │                                         [続きを読む] │ │
│ └─────────────────────────────────────────────────────┘ │
│                                                         │
│ ┌─────────────────────────────────────────────────────┐ │
│ │ 記事タイトル 2                                      │ │
│ │ 投稿日: 2024-01-02                                  │ │
│ │ 記事の要約が表示されます...                         │ │
│ │                                         [続きを読む] │ │
│ └─────────────────────────────────────────────────────┘ │
│                                                         │
│ [← 前のページ] [1] [2] [3] [次のページ →]               │
│                                                         │
├─────────────────────────────────────────────────────────┤
│ Footer: © 2024 ADR Blog Lite                            │
└─────────────────────────────────────────────────────────┘
```

**学習ポイント**:
- Actionでページネーション処理
- UseCaseで公開記事の取得
- Responderでページネーション情報の整形

### 2. 記事詳細画面（公開）

```
┌─────────────────────────────────────────────────────────┐
│ [Blog Title]                              [← 記事一覧]  │
├─────────────────────────────────────────────────────────┤
│                                                         │
│ 記事タイトルがここに表示されます                        │
│ 投稿日: 2024-01-01 | 著者: Blog Admin                  │
│                                                         │
│ ┌─────────────────────────────────────────────────────┐ │
│ │                                                     │ │
│ │ # マークダウンで書かれた記事内容                    │ │
│ │                                                     │ │
│ │ ここに記事の本文がHTMLとして表示されます。          │ │
│ │ マークダウンの見出し、リスト、コードブロック        │ │
│ │ などが適切にスタイリングされて表示されます。        │ │
│ │                                                     │ │
│ └─────────────────────────────────────────────────────┘ │
│                                                         │
│ ┌─────────────────────────────────────────────────────┐ │
│ │ [← 前の記事: タイトル]      [次の記事: タイトル →] │ │
│ └─────────────────────────────────────────────────────┘ │
│                                                         │
├─────────────────────────────────────────────────────────┤
│ Footer: © 2024 ADR Blog Lite                            │
└─────────────────────────────────────────────────────────┘
```

**学習ポイント**:
- ActionでURL パラメータの受け取り
- UseCaseで記事詳細とナビゲーション情報の取得
- ResponderでMarkdownのHTML変換

### 3. 記事管理一覧画面

```
┌─────────────────────────────────────────────────────────┐
│ 記事管理                               [新規投稿]        │
├─────────────────────────────────────────────────────────┤
│                                                         │
│ フィルタ: [全て ▼] [公開状態 ▼]           [検索_____] │
│                                                         │
│ ┌─────────────────────────────────────────────────────┐ │
│ │ ID │ タイトル        │ 状態    │ 投稿日    │ 操作  │ │
│ ├────┼─────────────────┼─────────┼───────────┼───────┤ │
│ │ 1  │ 記事タイトル1   │ 公開    │ 2024-01-01│[編集] │ │
│ │    │                 │         │           │[削除] │ │
│ ├────┼─────────────────┼─────────┼───────────┼───────┤ │
│ │ 2  │ 記事タイトル2   │ 下書き  │ 2024-01-02│[編集] │ │
│ │    │                 │         │           │[削除] │ │
│ ├────┼─────────────────┼─────────┼───────────┼───────┤ │
│ │ 3  │ 記事タイトル3   │ 公開    │ 2024-01-03│[編集] │ │
│ │    │                 │         │           │[削除] │ │
│ └────┴─────────────────┴─────────┴───────────┴───────┘ │
│                                                         │
│ [← 前のページ] [1] [2] [3] [次のページ →]               │
│                                                         │
├─────────────────────────────────────────────────────────┤
│ Footer: © 2024 ADR Blog Lite                            │
└─────────────────────────────────────────────────────────┘
```

**学習ポイント**:
- Actionでクエリパラメータの処理
- UseCaseで記事一覧の取得（フィルタ込み）
- ResponderでテーブルデータのHTML生成

### 4. 記事投稿・編集画面

```
┌─────────────────────────────────────────────────────────┐
│ 記事投稿/編集               [下書き保存] [公開] [キャンセル] │
├─────────────────────────────────────────────────────────┤
│                                                         │
│ タイトル:                                               │
│ [_____________________________________________________] │
│                                                         │
│ 本文:                                   [プレビュー切替] │
│ ┌─────────────────────┬─────────────────────────────────┐ │
│ │ # マークダウン記法  │ <h1>マークダウン記法</h1>       │ │
│ │                     │                                 │ │
│ │ ここに**記事**の    │ ここに<strong>記事</strong>の   │ │
│ │ 本文を書きます。    │ 本文を書きます。                │ │
│ │                     │                                 │ │
│ │ - リスト項目1       │ <ul>                           │ │
│ │ - リスト項目2       │   <li>リスト項目1</li>         │ │
│ │                     │   <li>リスト項目2</li>         │ │
│ │                     │ </ul>                          │ │
│ └─────────────────────┴─────────────────────────────────┘ │
│                                                         │
│ 公開状態: ( ) 下書き (●) 公開                           │
│                                                         │
├─────────────────────────────────────────────────────────┤
│ Footer: © 2024 ADR Blog Lite                            │
└─────────────────────────────────────────────────────────┘
```

**学習ポイント**:
- Actionでフォームデータの受け取り
- UseCaseで記事作成・更新処理
- ResponderでMarkdownプレビュー

## コンポーネント設計

### レイアウトテンプレート

#### app.blade.php（ベースレイアウト）
```php
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ADR Blog Lite')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4">
        @yield('content')
    </div>
</body>
</html>
```

#### manage.blade.php（記事管理レイアウト）
```php
@extends('layouts.app')

@section('content')
<header class="bg-white shadow mb-6">
    <div class="px-6 py-4 flex justify-between items-center">
        <h1 class="text-xl font-bold">記事管理</h1>
        <div class="space-x-4">
            <a href="{{ route('articles.create') }}" class="btn-primary">新規投稿</a>
            <a href="{{ route('articles.index') }}" class="btn-secondary">公開サイトへ</a>
        </div>
    </div>
</header>

<main>
    @yield('manage-content')
</main>
@endsection
```

### パーツコンポーネント

#### 記事カード
```php
{{-- resources/views/components/article-card.blade.php --}}
<article class="bg-white rounded-lg shadow p-6 mb-6">
    <h2 class="text-xl font-bold mb-2">
        <a href="{{ route('articles.show', $article) }}" class="hover:text-blue-600">
            {{ $article->title }}
        </a>
    </h2>
    <p class="text-gray-600 text-sm mb-3">
        投稿日: {{ $article->created_at->format('Y年m月d日') }}
    </p>
    <p class="text-gray-800 mb-4">
        {{ Str::limit(strip_tags($article->content), 200) }}
    </p>
    <a href="{{ route('articles.show', $article) }}" class="text-blue-600 hover:underline">
        続きを読む →
    </a>
</article>
```

#### ページネーション
```php
{{-- resources/views/components/pagination.blade.php --}}
@if ($paginator->hasPages())
<nav class="flex justify-center mt-8">
    <div class="flex space-x-2">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <span class="px-3 py-2 text-gray-400">← 前</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-2 text-blue-600 hover:underline">← 前</a>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="px-3 py-2">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="px-3 py-2 bg-blue-600 text-white rounded">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="px-3 py-2 text-blue-600 hover:underline">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-2 text-blue-600 hover:underline">次 →</a>
        @else
            <span class="px-3 py-2 text-gray-400">次 →</span>
        @endif
    </div>
</nav>
@endif
```

## CSSスタイリング

### Tailwind CSS設定例

#### tailwind.config.js
```js
/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Noto Sans JP', 'sans-serif'],
            },
        },
    },
    plugins: [
        require('@tailwindcss/typography'),
        require('@tailwindcss/forms'),
    ],
}
```

#### カスタムCSS（resources/css/app.css）
```css
@tailwind base;
@tailwind components;
@tailwind utilities;

@layer components {
    /* ボタンスタイル */
    .btn-primary {
        @apply bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition-colors;
    }

    .btn-secondary {
        @apply bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 transition-colors;
    }

    .btn-danger {
        @apply bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition-colors;
    }

    /* フォームスタイル */
    .form-input {
        @apply border border-gray-300 rounded px-3 py-2 w-full focus:border-blue-500 focus:outline-none;
    }

    .form-textarea {
        @apply border border-gray-300 rounded px-3 py-2 w-full h-64 focus:border-blue-500 focus:outline-none;
    }

    /* 記事コンテンツスタイル */
    .article-content {
        @apply prose prose-lg max-w-none;
    }
}
```

## JavaScript（バニラJS）

### マークダウンプレビュー機能
```html
{{-- 記事投稿・編集画面のプレビュー機能 --}}
<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium mb-2">本文（Markdown）</label>
        <textarea 
            id="markdown-content"
            name="content"
            class="form-textarea"
            placeholder="マークダウン形式で記事を書いてください"
        ></textarea>
    </div>
    <div>
        <label class="block text-sm font-medium mb-2">プレビュー</label>
        <div 
            id="markdown-preview"
            class="border border-gray-300 rounded p-3 h-64 overflow-y-auto bg-white article-content"
        ></div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const markdownContent = document.getElementById('markdown-content');
    const markdownPreview = document.getElementById('markdown-preview');
    
    if (markdownContent && markdownPreview) {
        markdownContent.addEventListener('input', function() {
            const content = this.value;
            // 簡単なマークダウン変換（実際の実装では marked.js などを使用）
            const html = content
                .replace(/^# (.*$)/gim, '<h1>$1</h1>')
                .replace(/^## (.*$)/gim, '<h2>$1</h2>')
                .replace(/\*\*(.*)\*\*/gim, '<strong>$1</strong>')
                .replace(/\n/gim, '<br>');
            
            markdownPreview.innerHTML = html;
        });
    }
});
</script>
```

## ルーティング設計（Web）

### routes/web.php
```php
<?php

use App\Actions\Web\Articles\IndexAction;
use App\Actions\Web\Articles\ShowAction;
use App\Actions\Web\Articles\ManageIndexAction;
use App\Actions\Web\Articles\CreateAction;
use App\Actions\Web\Articles\EditAction;
use App\Actions\Web\Articles\StoreAction;
use App\Actions\Web\Articles\UpdateAction;
use App\Actions\Web\Articles\DestroyAction;

// 公開画面
Route::get('/', IndexAction::class)->name('articles.index');
Route::get('/articles/{article}', ShowAction::class)->name('articles.show');

// 記事管理画面（認証なし・学習用）
Route::prefix('articles')->name('articles.')->group(function () {
    Route::get('/manage', ManageIndexAction::class)->name('manage');
    Route::get('/create', CreateAction::class)->name('create');
    Route::post('/', StoreAction::class)->name('store');
    Route::get('/{article}/edit', EditAction::class)->name('edit');
    Route::put('/{article}', UpdateAction::class)->name('update');
    Route::delete('/{article}', DestroyAction::class)->name('destroy');
});
```

## レスポンシブ対応

### ブレークポイント
- **モバイル**: 〜768px
- **タブレット**: 768px〜1024px
- **デスクトップ**: 1024px〜

### モバイル対応例
```html
{{-- 記事一覧のレスポンシブ対応 --}}
<div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
    @foreach($articles as $article)
        <x-article-card :article="$article" />
    @endforeach
</div>

{{-- ナビゲーションのモバイル対応 --}}
<nav class="bg-white shadow">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center py-4">
            <h1 class="text-xl font-bold">ADR Blog Lite</h1>
            
            {{-- モバイルメニューボタン --}}
            <button id="mobile-menu-button" class="md:hidden">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
            </button>
            
            {{-- デスクトップメニュー --}}
            <div class="hidden md:flex space-x-4">
                <a href="{{ route('articles.index') }}" class="hover:text-blue-600">記事一覧</a>
                <a href="{{ route('articles.manage') }}" class="hover:text-blue-600">記事管理</a>
            </div>
        </div>
        
        {{-- モバイルメニュー --}}
        <div id="mobile-menu" class="md:hidden hidden">
            <div class="py-2 space-y-1">
                <a href="{{ route('articles.index') }}" class="block px-4 py-2 hover:bg-gray-100">記事一覧</a>
                <a href="{{ route('articles.manage') }}" class="block px-4 py-2 hover:bg-gray-100">記事管理</a>
            </div>
        </div>
    </div>
</nav>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');
    
    if (mobileMenuButton && mobileMenu) {
        mobileMenuButton.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
        });
    }
});
</script>
```

## 学習のためのUI実装ポイント

### ADRパターンでのWeb画面
1. **Action**: フォーム処理、リクエスト受け取り
2. **UseCase**: データ取得・更新処理
3. **Responder**: Bladeテンプレートでの表示

### Bladeテンプレートでの型安全性
```php
{{-- resources/views/articles/show.blade.php --}}
@props(['article' => \App\Models\Article::class])

<article>
    <h1>{{ $article->title }}</h1>
    <p>投稿日: {{ $article->created_at->format('Y年m月d日') }}</p>
    <div class="article-content">
        {!! \Illuminate\Support\Str::markdown($article->content) !!}
    </div>
</article>
```

### フォームバリデーションの表示
```php
{{-- resources/views/admin/articles/form.blade.php --}}
<div>
    <label for="title" class="block text-sm font-medium mb-2">タイトル</label>
    <input 
        type="text" 
        id="title" 
        name="title" 
        value="{{ old('title', $article->title ?? '') }}"
        class="form-input @error('title') border-red-500 @enderror"
    >
    @error('title')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>
```

---

*このUI設計は、ADRパターンの学習に最適化されており、実際の本格的なWebアプリケーションでは、より詳細なUX設計やアクセシビリティ対応が必要です。* 