<header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Logo/Brand -->
            <div class="flex items-center">
                <a href="{{ route('home', []) ?? '/' }}" class="text-xl font-bold text-gray-800 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                    {{ config('app.name', 'ADR Blog Lite') }}
                </a>
            </div>

            <!-- Desktop Navigation -->
            <nav class="hidden md:flex items-center space-x-8">
                <a href="{{ route('articles.index', []) ?? '/articles' }}" 
                   class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white px-3 py-2 rounded-md text-sm font-medium transition-colors">
                    記事一覧
                </a>
                <a href="{{ route('articles.create', []) ?? '/articles/create' }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                    記事を投稿
                </a>
            </nav>

            <!-- Mobile menu button -->
            <div class="md:hidden flex items-center">
                <button type="button" 
                        class="mobile-menu-toggle text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white focus:outline-none focus:text-gray-900 dark:focus:text-white"
                        aria-controls="mobile-menu" 
                        aria-expanded="false">
                    <span class="sr-only">メニューを開く</span>
                    <!-- Hamburger icon -->
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div class="md:hidden hidden mobile-menu" id="mobile-menu">
        <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
            <a href="{{ route('articles.index', []) ?? '/articles' }}" 
               class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white block px-3 py-2 rounded-md text-base font-medium transition-colors">
                記事一覧
            </a>
            <a href="{{ route('articles.create', []) ?? '/articles/create' }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white block px-3 py-2 rounded-md text-base font-medium transition-colors">
                記事を投稿
            </a>
        </div>
    </div>
</header> 