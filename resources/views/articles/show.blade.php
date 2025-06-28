@extends('layouts.app')

@section('title', ($article->title ?? '記事詳細') . ' - ADR Blog Lite')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Breadcrumb -->
    <nav class="mb-8">
        <ol class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
            <li>
                <a href="{{ route('articles.index', []) ?? '/articles' }}" 
                   class="hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                    記事一覧
                </a>
            </li>
            <li>
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
            </li>
            <li class="text-gray-900 dark:text-white">記事詳細</li>
        </ol>
    </nav>

    <!-- Article Header -->
    <header class="mb-8">
        <div class="mb-4">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                テクノロジー
            </span>
        </div>
        
        <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4 leading-tight">
            {{ $article->title }}
        </h1>
        
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="h-10 w-10 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">著</span>
                    </div>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                        {{ $author->name }}
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $article->created_at->format('Y年m月d日') }}
                    </p>
                </div>
            </div>
            
            <!-- 公開中の記事ラベル -->
            <div class="flex items-center space-x-3">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    公開中
                </span>
            </div>
        </div>
    </header>

    <!-- Article Content -->
    <article class="prose prose-lg dark:prose-invert max-w-none">
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-8">
            <div class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $article->content }}</div>
        </div>
    </article>

    <!-- Navigation -->
    <nav class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-700">
        <div class="flex justify-between">
            <a href="{{ route('articles.index', []) ?? '/articles' }}" 
               class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                記事一覧に戻る
            </a>
            
            <span class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-gray-100 rounded-md cursor-not-allowed">
                新しい記事を作成（未実装）
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </span>
        </div>
    </nav>
</div>
@endsection 