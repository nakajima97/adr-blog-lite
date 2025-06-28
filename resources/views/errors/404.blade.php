@extends('layouts.app')

@section('title', ' - ページが見つかりません')

@section('content')
<div class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full text-center">
        <!-- Error Illustration -->
        <div class="mb-8">
            <svg class="mx-auto h-32 w-32 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
        </div>

        <!-- Error Code -->
        <div class="mb-4">
            <h1 class="text-6xl font-bold text-gray-900 dark:text-white">404</h1>
        </div>

        <!-- Error Message -->
        <div class="mb-8">
            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-2">
                ページが見つかりません
            </h2>
            <p class="text-gray-600 dark:text-gray-400">
                お探しのページは存在しないか、移動した可能性があります。<br>
                URLを確認するか、ホームページから再度お探しください。
            </p>
        </div>

        <!-- Action Buttons -->
        <div class="space-y-4 sm:space-y-0 sm:space-x-4 sm:flex sm:justify-center">
            <a href="{{ route('articles.index', []) ?? '/' }}" 
               class="inline-flex items-center justify-center w-full sm:w-auto px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                ホームに戻る
            </a>
            
            <button onclick="history.back()" 
                    class="inline-flex items-center justify-center w-full sm:w-auto px-6 py-3 border border-gray-300 dark:border-gray-600 text-base font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                前のページに戻る
            </button>
        </div>

        <!-- Help Links -->
        <div class="mt-8 pt-8 border-t border-gray-200 dark:border-gray-700">
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                お困りですか？以下のリンクもお試しください：
            </p>
            <div class="flex justify-center space-x-6">
                <a href="{{ route('articles.index', []) ?? '/articles' }}" 
                   class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 transition-colors">
                    記事一覧
                </a>
                <a href="#" 
                   class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 transition-colors">
                    お問い合わせ
                </a>
                <a href="#" 
                   class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 transition-colors">
                    サイトマップ
                </a>
            </div>
        </div>
    </div>
</div>
@endsection 