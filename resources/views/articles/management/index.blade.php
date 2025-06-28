@extends('layouts.app')

@section('title', '記事管理')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- ヘッダー部分 -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">記事管理</h1>
        <p class="text-gray-600">登録されている記事 {{ $totalCount }} 件</p>
    </div>

    <!-- フィルタリングフォーム -->
    <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6 shadow-sm">
        <form method="GET" action="{{ route('articles.management.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- ステータスフィルタ -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">ステータス</label>
                    <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @foreach($statusOptions as $option)
                            <option value="{{ $option['value'] }}" 
                                    {{ $activeStatusFilter === $option['value'] ? 'selected' : '' }}>
                                {{ $option['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 検索クエリ -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">検索</label>
                    <input type="text" name="search" id="search" value="{{ $searchQuery }}" 
                           placeholder="タイトル・内容で検索"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- 日付範囲（開始） -->
                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">作成日（開始）</label>
                    <input type="date" name="date_from" id="date_from" value="{{ $dateFrom }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- 日付範囲（終了） -->
                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">作成日（終了）</label>
                    <input type="date" name="date_to" id="date_to" value="{{ $dateTo }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="flex justify-between items-center">
                <div class="flex space-x-2">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        <i class="fas fa-search mr-1"></i>
                        検索
                    </button>
                    <a href="{{ route('articles.management.index') }}" 
                       class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition-colors">
                        <i class="fas fa-times mr-1"></i>
                        クリア
                    </a>
                </div>
            </div>
        </form>
    </div>

    @if($articles->count() > 0)
        <!-- 記事一覧テーブル -->
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                記事情報
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ステータス
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                投稿者
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                日時
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                操作
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($articles as $article)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $article->title }}
                                        </div>
                                        <div class="text-sm text-gray-500 mt-1">
                                            {{ Str::limit(strip_tags($article->content), 100) }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($article->status === 'published')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            公開済み
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-edit mr-1"></i>
                                            下書き
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $article->user->name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div>作成: {{ $article->created_at->format('Y/m/d H:i') }}</div>
                                    <div>更新: {{ $article->updated_at->format('Y/m/d H:i') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    <a href="{{ route('articles.show', $article->id) }}" 
                                       class="text-blue-600 hover:text-blue-900" title="詳細表示">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="#" class="text-green-600 hover:text-green-900" title="編集">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="text-red-600 hover:text-red-900" title="削除">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ページネーション -->
        @if($hasPages)
            <div class="mt-6">
                <nav class="flex justify-center">
                    <div class="flex items-center space-x-2">
                        {{-- 前へボタン --}}
                        @if($articles->previousPageUrl())
                            <a href="{{ $articles->appends(request()->query())->previousPageUrl() }}" 
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
                            <a href="{{ $articles->appends(request()->query())->nextPageUrl() }}" 
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
            <h3 class="text-xl font-medium text-gray-900 mb-2">条件に一致する記事がありません</h3>
            <p class="text-gray-600">検索条件を変更してお試しください。</p>
        </div>
    @endif
</div>
@endsection 