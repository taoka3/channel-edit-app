<!DOCTYPE html>
<html lang="ja" class="antialiased" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))" :class="{ 'dark': darkMode }">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YouTube Channel Edit</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        yt: '#FF0000',
                    }
                }
            }
        }
    </script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="bg-gray-50 text-gray-900 transition-colors duration-200 dark:bg-gray-900 dark:text-gray-100 min-h-screen">

    <!-- Header -->
    <header class="absolute top-0 w-full z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center gap-2">
                    <span class="text-3xl text-yt">▶</span>
                    <span class="text-xl font-bold tracking-tight">Channel Edit</span>
                </div>
                <div class="flex items-center gap-4">
                    <button @click="darkMode = !darkMode" class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none transition-colors">
                        <svg x-show="!darkMode" class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                        </svg>
                        <svg x-show="darkMode" class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </button>
                    @auth
                    <a href="{{ route('dashboard') }}" class="font-medium text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white transition-colors">ダッシュボード</a>
                    @else
                    <a href="{{ route('login') }}" class="bg-yt hover:bg-red-700 text-white font-medium py-2 px-5 rounded-full shadow-lg transition duration-200 transform hover:scale-105">ログイン</a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <div class="relative overflow-hidden min-h-screen flex items-center">
        <!-- Background Decoration -->
        <div class="absolute inset-0 z-0">
            <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 rounded-full bg-red-100 dark:bg-red-900/20 blur-3xl opacity-50"></div>
            <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 rounded-full bg-blue-100 dark:bg-blue-900/20 blur-3xl opacity-50"></div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full pt-20">
            <div class="text-center md:text-left md:flex items-center justify-between gap-12">
                <div class="max-w-2xl">
                    <h1 class="text-4xl sm:text-5xl md:text-6xl font-extrabold text-gray-900 dark:text-white tracking-tight leading-tight mb-6">
                        登録チャンネルを<br />
                        <span class="text-yt relative inline-block">
                            もっとスマートに
                            <svg class="absolute w-full h-3 -bottom-1 left-0 text-red-300 dark:text-red-800/50" viewBox="0 0 100 10" preserveAspectRatio="none">
                                <path d="M0 5 Q 50 10 100 5" stroke="currentColor" stroke-width="3" fill="transparent" />
                            </svg>
                        </span><br />
                        整理しよう。
                    </h1>
                    <p class="text-lg md:text-xl text-gray-600 dark:text-gray-400 mb-10 leading-relaxed">
                        YouTube Data APIを活用し、あなたの登録チャンネルをカテゴリや優先度で一元管理。見たい動画をいつでもすぐに見つけられる環境を提供します。
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center md:justify-start">
                        <a href="{{ route('login') }}" class="bg-yt hover:bg-red-700 text-white text-lg font-bold py-4 px-8 rounded-full shadow-xl transition-all duration-200 transform hover:-translate-y-1 hover:shadow-2xl flex items-center justify-center gap-2">
                            無料で始める
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </a>
                        <a href="#features" class="bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700 text-lg font-bold py-4 px-8 rounded-full shadow-md transition-all duration-200 flex items-center justify-center">
                            機能を見る
                        </a>
                    </div>
                </div>

                <div class="hidden md:block w-full max-w-lg mt-12 md:mt-0 relative">
                    <!-- Dashboard Mockup Image -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-700 overflow-hidden transform rotate-2 hover:rotate-0 transition-transform duration-500">
                        <!-- Mac OS Window Controls -->
                        <div class="bg-gray-100 dark:bg-gray-900 px-4 py-3 flex items-center gap-2 border-b border-gray-200 dark:border-gray-700">
                            <div class="w-3 h-3 rounded-full bg-red-500"></div>
                            <div class="w-3 h-3 rounded-full bg-yellow-400"></div>
                            <div class="w-3 h-3 rounded-full bg-green-500"></div>
                        </div>
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-6">
                                <div class="h-6 w-32 bg-gray-200 dark:bg-gray-700 rounded"></div>
                                <div class="h-8 w-24 bg-red-100 dark:bg-red-900/30 rounded-lg"></div>
                            </div>
                            <div class="space-y-4">
                                <div class="flex items-center gap-4 p-3 rounded-xl bg-gray-50 dark:bg-gray-700/50">
                                    <div class="w-10 h-10 rounded-full bg-gray-300 dark:bg-gray-600"></div>
                                    <div class="flex-1 space-y-2">
                                        <div class="h-4 w-3/4 bg-gray-300 dark:bg-gray-600 rounded"></div>
                                        <div class="h-3 w-1/2 bg-gray-200 dark:bg-gray-700 rounded"></div>
                                    </div>
                                    <div class="h-6 w-16 bg-red-100 dark:bg-red-900/50 rounded-full"></div>
                                </div>
                                <div class="flex items-center gap-4 p-3 rounded-xl bg-gray-50 dark:bg-gray-700/50">
                                    <div class="w-10 h-10 rounded-full bg-gray-300 dark:bg-gray-600"></div>
                                    <div class="flex-1 space-y-2">
                                        <div class="h-4 w-2/3 bg-gray-300 dark:bg-gray-600 rounded"></div>
                                        <div class="h-3 w-1/3 bg-gray-200 dark:bg-gray-700 rounded"></div>
                                    </div>
                                    <div class="h-6 w-16 bg-yellow-100 dark:bg-yellow-900/50 rounded-full"></div>
                                </div>
                                <div class="flex items-center gap-4 p-3 rounded-xl bg-gray-50 dark:bg-gray-700/50">
                                    <div class="w-10 h-10 rounded-full bg-gray-300 dark:bg-gray-600"></div>
                                    <div class="flex-1 space-y-2">
                                        <div class="h-4 w-4/5 bg-gray-300 dark:bg-gray-600 rounded"></div>
                                        <div class="h-3 w-2/5 bg-gray-200 dark:bg-gray-700 rounded"></div>
                                    </div>
                                    <div class="h-6 w-16 bg-gray-200 dark:bg-gray-600 rounded-full"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div id="features" class="py-24 bg-white dark:bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">シンプルな機能で劇的に整理</h2>
                <p class="text-gray-600 dark:text-gray-400">必要な機能だけを厳選。迷うことなくチャンネルを管理できます。</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                <div class="bg-gray-50 dark:bg-gray-800 rounded-2xl p-8 hover:shadow-xl transition-shadow duration-300">
                    <div class="w-14 h-14 bg-red-100 dark:bg-red-900/30 text-yt rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">カテゴリ・優先度管理</h3>
                    <p class="text-gray-600 dark:text-gray-400">独自のカテゴリを作成し、必ず見たい「優先度1」から時々見る「優先度3」まで細かく分類できます。</p>
                </div>

                <div class="bg-gray-50 dark:bg-gray-800 rounded-2xl p-8 hover:shadow-xl transition-shadow duration-300">
                    <div class="w-14 h-14 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">最新更新をキャッチ</h3>
                    <p class="text-gray-600 dark:text-gray-400">API連携により、登録チャンネルが最後に動画をアップロードした日時を取得。アクティブなチャンネルが一目でわかります。</p>
                </div>

                <div class="bg-gray-50 dark:bg-gray-800 rounded-2xl p-8 hover:shadow-xl transition-shadow duration-300">
                    <div class="w-14 h-14 bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">メモで詳細を記録</h3>
                    <p class="text-gray-600 dark:text-gray-400">「なぜ登録したのか」「どの企画が面白かったか」など、チャンネルごとに自由なメモを残せます。</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="flex items-center justify-center gap-2 mb-4">
                <span class="text-2xl text-yt">▶</span>
                <span class="text-lg font-bold text-gray-900 dark:text-white">Channel Edit</span>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                &copy; {{ date('Y') }} YouTube Channel Edit. All rights reserved.
            </p>
        </div>
    </footer>
</body>

</html>