@extends('layouts.app')

@section('title', ' - 記事詳細')

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
            {{-- {{ $article->title ?? 'サンプル記事: ADRパターンの実装について' }} --}}
            サンプル記事: ADRパターンの実装について
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
                        {{-- {{ $article->author ?? '著者名' }} --}}
                        著者名
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{-- {{ $article->created_at->format('Y年m月d日') ?? now()->format('Y年m月d日') }} --}}
                        {{ now()->format('Y年m月d日') }}
                    </p>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex items-center space-x-3">
                <a href="{{ route('articles.edit', ['article' => 1]) ?? '/articles/1/edit' }}" 
                   class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    編集
                </a>
                <button type="button" 
                        class="inline-flex items-center px-3 py-2 text-sm font-medium text-red-700 dark:text-red-400 bg-white dark:bg-gray-800 border border-red-300 dark:border-red-600 rounded-md hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    削除
                </button>
            </div>
        </div>
    </header>

    <!-- Article Content -->
    <article class="prose prose-lg dark:prose-invert max-w-none">
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-8">
            {{-- {!! $article->content ?? $sampleContent !!} --}}
            <p class="text-lg text-gray-700 dark:text-gray-300 mb-6">
                Action-Domain-Responder（ADR）パターンは、Webアプリケーションの設計パターンの一つで、従来のMVCパターンの問題点を解決するために提案されました。
            </p>
            
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4 mt-8">ADRパターンとは</h2>
            <p class="text-gray-700 dark:text-gray-300 mb-4">
                ADRパターンは以下の3つの層で構成されます：
            </p>
            
            <ul class="list-disc list-inside text-gray-700 dark:text-gray-300 mb-6 space-y-2">
                <li><strong>Action</strong>: リクエストを受け取り、Domainへの処理を委譲する</li>
                <li><strong>Domain</strong>: ビジネスロジックを含む核となる処理</li>
                <li><strong>Responder</strong>: レスポンス生成の責務を担う</li>
            </ul>
            
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4 mt-8">実装例</h2>
            <p class="text-gray-700 dark:text-gray-300 mb-4">
                以下は、Laravelでの基本的なADRパターンの実装例です：
            </p>
            
            <pre class="bg-gray-100 dark:bg-gray-900 p-4 rounded-lg overflow-x-auto text-sm"><code class="language-php">// Action
class ListArticlesAction
{
    public function __invoke(Request $request, ListArticlesUseCase $useCase, ArticleListResponder $responder)
    {
        $articles = $useCase->execute();
        return $responder->respond($articles);
    }
}</code></pre>
            
            <p class="text-gray-700 dark:text-gray-300 mt-6">
                このように、各層の責務を明確に分離することで、保守性の高いコードを実現できます。
            </p>
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
            
            <a href="{{ route('articles.create', []) ?? '/articles/create' }}" 
               class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md transition-colors">
                新しい記事を作成
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                </svg>
            </a>
        </div>
    </nav>
</div>
@endsection 