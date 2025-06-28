@extends('layouts.app')

@section('title', '記事一覧')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- ヘッダー部分 -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">記事一覧</h1>
        <p class="text-gray-600">公開中の記事 {{ $totalCount }} 件</p>
    </div>

    @if($articles->count() > 0)
        <!-- 記事一覧 -->
        <div class="space-y-6">
            @foreach($articles as $article)
                <article class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm hover:shadow-md transition-shadow">
                    <header class="mb-3">
                        <h2 class="text-xl font-semibold text-gray-900 mb-2">
                            {{ $article->title }}
                        </h2>
                        <div class="flex items-center text-sm text-gray-500 space-x-4">
                            <span>
                                <i class="fas fa-user mr-1"></i>
                                {{ $article->user->name }}
                            </span>
                            <span>
                                <i class="fas fa-calendar mr-1"></i>
                                {{ $article->created_at->format('Y年m月d日') }}
                            </span>
                        </div>
                    </header>
                    
                    <div class="text-gray-700 leading-relaxed">
                        {{ Str::limit(strip_tags($article->content), 200) }}
                    </div>
                    
                    <footer class="mt-4">
                        <a href="{{ route('articles.show', $article->id) }}" 
                           class="inline-flex items-center text-blue-600 hover:text-blue-800 transition-colors">
                            続きを読む
                            <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </footer>
                </article>
            @endforeach
        </div>

        <!-- ページネーション -->
        @if($hasPages)
            <div class="mt-8">
                <nav class="flex justify-center">
                    <div class="flex items-center space-x-2">
                        {{-- 前へボタン --}}
                        @if($articles->previousPageUrl())
                            <a href="{{ $articles->previousPageUrl() }}" 
                               class="px-3 py-2 bg-white border border-gray-300 rounded-md hover:bg-gray-50 text-sm font-medium text-gray-700">
                                ← 前へ
                            </a>
                        @endif

                        {{-- ページ番号 --}}
                        <span class="px-3 py-2 text-sm text-gray-700">
                            {{ $currentPage }} / {{ $lastPage }} ページ
                        </span>

                        {{-- 次へボタン --}}
                        @if($articles->nextPageUrl())
                            <a href="{{ $articles->nextPageUrl() }}" 
                               class="px-3 py-2 bg-white border border-gray-300 rounded-md hover:bg-gray-50 text-sm font-medium text-gray-700">
                                次へ →
                            </a>
                        @endif
                    </div>
                </nav>
            </div>
        @endif
    @else
        <!-- 記事がない場合 -->
        <div class="text-center py-12">
            <div class="text-gray-400 mb-4">
                <i class="fas fa-file-alt text-6xl"></i>
            </div>
            <h3 class="text-xl font-medium text-gray-900 mb-2">記事がありません</h3>
            <p class="text-gray-600">まだ公開されている記事がありません。</p>
        </div>
    @endif
</div>
@endsection 