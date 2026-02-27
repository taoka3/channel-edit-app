@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">ダッシュボード</h2>

    <form action="{{ route('channels.sync') }}" method="POST">
        @csrf
        <button type="submit" class="bg-yt hover:bg-red-700 text-white font-bold py-2 px-4 rounded-xl shadow transition duration-200 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            YouTube同期
        </button>
    </form>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Stat 1 -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex flex-col items-center justify-center transition-transform hover:scale-105">
        <div class="text-gray-500 dark:text-gray-400 text-sm font-medium mb-1">総チャンネル数</div>
        <div class="text-4xl font-extrabold text-gray-900 dark:text-white">{{ $totalChannels }}</div>
    </div>

    <!-- Stat 2 -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex flex-col items-center justify-center transition-transform hover:scale-105">
        <div class="text-gray-500 dark:text-gray-400 text-sm font-medium mb-1">優先度1 (毎日見る)</div>
        <div class="text-4xl font-extrabold text-yt">{{ $priorityOneChannels }}</div>
    </div>

    <!-- Stat 3 -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex flex-col items-center justify-center transition-transform hover:scale-105">
        <div class="text-gray-500 dark:text-gray-400 text-sm font-medium mb-1">直近1週間の更新</div>
        <div class="text-4xl font-extrabold text-green-500">{{ $recentChannels }}</div>
    </div>
</div>
@endsection