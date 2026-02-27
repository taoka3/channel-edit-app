@extends('layouts.app')

@section('title', 'カテゴリ管理 | YouTube Channel Edit')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">カテゴリ管理</h2>
</div>

<div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl border border-gray-100 dark:border-gray-700 mb-8 p-6">
    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">新規作成</h3>
    <form action="{{ route('categories.store') }}" method="POST" class="flex items-center gap-4">
        @csrf
        <div class="flex-grow">
            <input type="text" name="name" placeholder="カテゴリ名を入力..." required
                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm focus:ring-yt focus:border-yt dark:bg-gray-900 dark:text-white transition-colors duration-200">
        </div>
        <button type="submit" class="bg-yt hover:bg-red-700 text-white font-medium py-3 px-6 rounded-xl shadow transition duration-200">
            作成
        </button>
    </form>
</div>

<div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
    <ul class="divide-y divide-gray-200 dark:divide-gray-700">
        @forelse($categories as $category)
        <li class="p-6 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors" x-data="{ editing: false }">

            <div x-show="!editing" class="flex-grow font-medium text-gray-900 dark:text-gray-100">
                {{ $category->name }}
            </div>

            <form x-show="editing" action="{{ route('categories.update', $category) }}" method="POST" class="flex-grow flex items-center gap-4 mr-4" style="display: none;">
                @csrf
                @method('PUT')
                <input type="text" name="name" value="{{ $category->name }}" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-yt focus:border-yt dark:bg-gray-900 dark:text-white">
                <button type="submit" class="text-sm bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg">保存</button>
                <button type="button" @click="editing = false" class="text-sm bg-gray-200 hover:bg-gray-300 text-gray-800 py-2 px-4 rounded-lg">キャンセル</button>
            </form>

            <div x-show="!editing" class="flex items-center gap-2">
                <button @click="editing = true" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium text-sm px-3 py-1">編集</button>

                <form action="{{ route('categories.destroy', $category) }}" method="POST" onsubmit="return confirm('本当に削除しますか？紐づくチャンネルのカテゴリは未分類になります。');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 font-medium text-sm px-3 py-1">削除</button>
                </form>
            </div>
        </li>
        @empty
        <li class="p-6 text-center text-gray-500 dark:text-gray-400">
            カテゴリが登録されていません。
        </li>
        @endforelse
    </ul>
</div>
@endsection