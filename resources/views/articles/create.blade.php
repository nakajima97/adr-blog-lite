@extends('layouts.app')

@section('title', ' - 記事作成')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumb -->
    <nav class="mb-8">
        <ol class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
            <li>
                <a href="{{ route('articles.management.index', []) ?? '/articles' }}" 
                   class="hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                    記事一覧
                </a>
            </li>
            <li>
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
            </li>
            <li class="text-gray-900 dark:text-white">記事作成</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">新しい記事を作成</h1>
        <p class="mt-2 text-gray-600 dark:text-gray-400">
            読者にとって価値のあるコンテンツを作成しましょう
        </p>
    </div>

    <!-- Form -->
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
        <form action="{{ route('articles.store', []) ?? '/articles' }}" method="POST" class="p-8">
            @csrf
            
            <!-- Title Field -->
            <div class="mb-6">
                <label for="title" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                    記事タイトル <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       id="title" 
                       name="title" 
                       value="{{ old('title') }}" 
                       class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 dark:placeholder-gray-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('title') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror" 
                       placeholder="魅力的なタイトルを入力してください"
                       required>
                @error('title')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Category Field -->
            <div class="mb-6">
                <label for="category" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                    カテゴリー
                </label>
                <select id="category" 
                        name="category" 
                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('category') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror">
                    <option value="">カテゴリーを選択してください</option>
                    <option value="technology" {{ old('category') === 'technology' ? 'selected' : '' }}>テクノロジー</option>
                    <option value="design" {{ old('category') === 'design' ? 'selected' : '' }}>デザイン</option>
                    <option value="business" {{ old('category') === 'business' ? 'selected' : '' }}>ビジネス</option>
                    <option value="lifestyle" {{ old('category') === 'lifestyle' ? 'selected' : '' }}>ライフスタイル</option>
                    <option value="other" {{ old('category') === 'other' ? 'selected' : '' }}>その他</option>
                </select>
                @error('category')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Content Field -->
            <div class="mb-6">
                <label for="content" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                    記事内容 <span class="text-red-500">*</span>
                </label>
                <textarea id="content" 
                          name="content" 
                          rows="15" 
                          class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 dark:placeholder-gray-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('content') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror" 
                          placeholder="記事の内容をMarkdown形式で入力してください..."
                          required>{{ old('content') }}</textarea>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Markdown記法をサポートしています。**太字**、*斜体*、[リンク](URL)、コードブロックなどを使用できます。
                </p>
                @error('content')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tags Field -->
            <div class="mb-6">
                <label for="tags" class="block text-sm font-medium text-gray-900 dark:text-white mb-2">
                    タグ
                </label>
                <input type="text" 
                       id="tags" 
                       name="tags" 
                       value="{{ old('tags') }}" 
                       class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 dark:placeholder-gray-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('tags') border-red-300 focus:ring-red-500 focus:border-red-500 @enderror" 
                       placeholder="Laravel, PHP, ADR (カンマ区切りで入力)">
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    関連するタグをカンマ区切りで入力してください。
                </p>
                @error('tags')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status Field -->
            <div class="mb-8">
                <label class="block text-sm font-medium text-gray-900 dark:text-white mb-3">
                    公開状態
                </label>
                <div class="space-y-2">
                    <div class="flex items-center">
                        <input id="status_draft" 
                               name="status" 
                               type="radio" 
                               value="draft" 
                               {{ old('status', 'draft') === 'draft' ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600">
                        <label for="status_draft" class="ml-3 text-sm text-gray-900 dark:text-white">
                            下書き（後で編集可能）
                        </label>
                    </div>
                    <div class="flex items-center">
                        <input id="status_published" 
                               name="status" 
                               type="radio" 
                               value="published" 
                               {{ old('status') === 'published' ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-gray-600">
                        <label for="status_published" class="ml-3 text-sm text-gray-900 dark:text-white">
                            すぐに公開
                        </label>
                    </div>
                </div>
                @error('status')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('articles.index', []) ?? '/articles' }}" 
                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    キャンセル
                </a>
                
                <div class="flex items-center space-x-3">
                    <button type="button" 
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        プレビュー
                    </button>
                    <button type="submit" 
                            class="inline-flex items-center px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        記事を作成
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection 