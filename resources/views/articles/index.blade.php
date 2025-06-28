@extends('layouts.app')

@section('title', ' - 記事一覧')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="md:flex md:items-center md:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">記事一覧</h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">
                    ブログの最新記事をお楽しみください
                </p>
            </div>
            <div class="mt-4 md:mt-0">
                <a href="{{ route('articles.create', []) ?? '/articles/create' }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    新しい記事を作成
                </a>
            </div>
        </div>
    </div>

    <!-- Articles Grid -->
    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        <!-- Sample Article Card (for demonstration) -->
        @for ($i = 1; $i <= 6; $i++)
        <article class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                        テクノロジー
                    </span>
                    <time class="text-sm text-gray-500 dark:text-gray-400">
                        {{ now()->subDays($i)->format('Y年m月d日') }}
                    </time>
                </div>
                
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-3 line-clamp-2">
                    サンプル記事 {{ $i }}: ADRパターンの実装について
                </h2>
                
                <p class="text-gray-600 dark:text-gray-400 mb-4 line-clamp-3">
                    この記事では、Action-Domain-Responderパターンの実装方法について詳しく解説します。Laravel アプリケーションでの具体的な実装例も含めて説明していきます。
                </p>
                
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-8 w-8 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                <span class="text-xs font-medium text-gray-700 dark:text-gray-300">著</span>
                            </div>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">著者名</p>
                        </div>
                    </div>
                    <a href="{{ route('articles.show', ['article' => $i]) ?? '/articles/' . $i }}" 
                       class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-medium text-sm transition-colors">
                        続きを読む →
                    </a>
                </div>
            </div>
        </article>
        @endfor
    </div>

    <!-- Empty State (when no articles exist) -->
    {{-- @if(empty($articles))
    <div class="text-center py-16">
        <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">記事がありません</h3>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">最初の記事を投稿してみましょう。</p>
        <div class="mt-6">
            <a href="{{ route('articles.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-md transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                新しい記事を作成
            </a>
        </div>
    </div>
    @endif --}}

    <!-- Pagination (for future use) -->
    {{-- <div class="mt-8">
        {{ $articles->links() }}
    </div> --}}
</div>
@endsection 