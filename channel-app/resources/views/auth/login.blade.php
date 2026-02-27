@extends('layouts.app')

@section('title', 'ログイン | YouTube Channel Edit')

@section('content')
<div class="min-h-[80vh] flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
    <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white dark:bg-gray-800 shadow-xl overflow-hidden sm:rounded-2xl border border-gray-100 dark:border-gray-700">

        <div class="mb-8 text-center">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center justify-center gap-2">
                <span class="text-yt">▶</span> Channel Edit
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">チャンネルを整理・管理するダッシュボード</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">メールアドレス</label>
                <div class="mt-1">
                    <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="[EMAIL_ADDRESS]" required autofocus class="appearance-none block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-yt focus:border-yt dark:bg-gray-900 dark:text-white transition-colors duration-200">
                </div>
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">パスワード</label>
                <div class="mt-1">
                    <input id="password" type="password" name="password" value="" required class="appearance-none block w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-yt focus:border-yt dark:bg-gray-900 dark:text-white transition-colors duration-200">
                </div>
            </div>

            <div>
                <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-yt hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yt transition-colors duration-200 dark:focus:ring-offset-gray-900">
                    ログイン
                </button>
            </div>
        </form>
    </div>
</div>
@endsection