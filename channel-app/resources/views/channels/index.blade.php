@extends('layouts.app')

@section('title', 'チャンネル一覧 | YouTube Channel Edit')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-center gap-4">
    <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">チャンネル一覧</h2>

    <div class="flex flex-col sm:flex-row items-center gap-4 w-full sm:w-auto">
        <form action="{{ route('channels.index') }}" method="GET" class="flex items-center gap-2 w-full sm:w-auto">
            <div class="relative w-full sm:w-64">
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="チャンネル名を検索..."
                    class="w-full text-sm border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-800 dark:text-gray-200 focus:ring-yt focus:border-yt pl-9">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            <select name="sort" class="text-sm border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-800 dark:text-gray-200">
                <option value="last_video_at" {{ $sort === 'last_video_at' ? 'selected' : '' }}>更新日</option>
                <option value="subscriber_count" {{ $sort === 'subscriber_count' ? 'selected' : '' }}>登録者数</option>
            </select>
            <select name="direction" class="text-sm border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-800 dark:text-gray-200">
                <option value="desc" {{ $direction === 'desc' ? 'selected' : '' }}>降順</option>
                <option value="asc" {{ $direction === 'asc' ? 'selected' : '' }}>昇順</option>
            </select>
            <button type="submit" class="bg-gray-200 hover:bg-gray-300 text-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-200 text-sm py-2 px-3 rounded-lg transition-colors">検索</button>
            @if($search)
            <a href="{{ route('channels.index') }}" class="text-xs text-gray-500 hover:text-yt underline">クリア</a>
            @endif
        </form>
    </div>
</div>

<div x-data="{ editModalOpen: false, currentChannel: null }">
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl border border-gray-100 dark:border-gray-700 overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-900/50">
                <tr>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">サムネ / チャンネル名</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">登録者 / 動画数</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">優先度 / カテゴリ</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">更新日 / メモ</th>
                    <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">操作</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($channels as $channel)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center gap-4">
                            @if($channel->thumbnail)
                            <img src="{{ $channel->thumbnail }}" alt="{{ $channel->title }}" class="w-12 h-12 rounded-full object-cover">
                            @else
                            <div class="w-12 h-12 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-gray-500">No</div>
                            @endif
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100 max-w-[200px] truncate" title="{{ $channel->title }}">
                                <a href="https://youtube.com/channel/{{ $channel->youtube_channel_id }}" target="_blank" class="hover:text-yt transition-colors">{{ $channel->title }}</a>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900 dark:text-gray-200">{{ number_format($channel->subscriber_count) }} 人</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ number_format($channel->video_count) }} 動画</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex flex-col gap-1 items-start">
                            @php
                            $badgeColor = match($channel->priority) {
                            1 => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                            2 => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
                            default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                            };
                            @endphp
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $badgeColor }}">
                                優先度 {{ $channel->priority }}
                            </span>
                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $channel->category ? $channel->category->name : '未分類' }}
                            </span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900 dark:text-gray-200">
                            {{ $channel->last_video_at ? $channel->last_video_at->format('Y/m/d H:i') : '不明' }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 max-w-[200px] truncate" title="{{ $channel->memo }}">
                            {{ $channel->memo ?: '-' }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex items-center justify-end gap-3">
                            <button type="button"
                                @click="
                                            currentChannel = {
                                                id: {{ $channel->id }},
                                                title: '{{ addslashes($channel->title) }}',
                                                category_id: '{{ $channel->category_id }}',
                                                priority: {{ $channel->priority }},
                                                memo: '{{ addslashes(preg_replace('/\r|\n/', '\\n', $channel->memo ?? '')) }}'
                                            };
                                            editModalOpen = true;
                                        "
                                class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                編集
                            </button>

                            <!-- 削除ボタン -->
                            <form action="{{ route('channels.destroy', $channel) }}" method="POST" onsubmit="return confirm('本当に削除しますか？');" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 cursor-pointer text-sm">
                                    削除
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                        チャンネルがありません。「ダッシュボード」から同期を行ってください。
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Edit Modal (Alpine.js) -->
    <div x-show="editModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <!-- Background backdrop -->
        <div x-show="editModalOpen"
            x-transition.opacity
            class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity"
            @click="editModalOpen = false"></div>

        <!-- Modal panel -->
        <div class="flex items-center justify-center min-h-screen p-4 sm:p-0">
            <div x-show="editModalOpen"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl transform transition-all sm:max-w-lg sm:w-full border border-gray-100 dark:border-gray-700" @click.stop>
                <form :action="`/channels/${currentChannel?.id}`" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="px-6 py-6 sm:p-8">
                        <div class="flex justify-between items-start mb-6">
                            <h3 class="text-xl leading-6 font-bold text-gray-900 dark:text-white" x-text="currentChannel?.title">チャンネル編集</h3>
                            <button type="button" @click="editModalOpen = false" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                                <span class="sr-only">閉じる</span>
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="space-y-5">
                            <div>
                                <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">カテゴリ</label>
                                <select name="category_id" id="category_id" x-model="currentChannel && currentChannel.category_id" class="mt-1 block w-full pl-3 pr-10 py-3 text-base border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-yt focus:border-yt sm:text-sm rounded-xl dark:bg-gray-900 dark:text-white transition-colors duration-200">
                                    <option value="">未分類</option>
                                    @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300">優先度</label>
                                <select name="priority" id="priority" x-model="currentChannel && currentChannel.priority" class="mt-1 block w-full pl-3 pr-10 py-3 text-base border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-yt focus:border-yt sm:text-sm rounded-xl dark:bg-gray-900 dark:text-white transition-colors duration-200">
                                    <option value="1">1 (毎日見る)</option>
                                    <option value="2">2 (時々)</option>
                                    <option value="3">3 (ほぼ見ない)</option>
                                </select>
                            </div>

                            <div>
                                <label for="memo" class="block text-sm font-medium text-gray-700 dark:text-gray-300">メモ</label>
                                <div class="mt-1">
                                    <textarea id="memo" name="memo" rows="3" x-model="currentChannel && currentChannel.memo" class="shadow-sm focus:ring-yt focus:border-yt mt-1 block w-full sm:text-sm border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-900 dark:text-white transition-colors duration-200 p-3" placeholder="チャンネルに関するメモ等..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 sm:px-8 sm:flex sm:flex-row-reverse border-t border-gray-100 dark:border-gray-700 rounded-b-2xl">
                        <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-6 py-3 bg-yt text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yt sm:ml-3 sm:w-auto sm:text-sm transition-colors duration-200">
                            保存
                        </button>
                        <button type="button" @click="editModalOpen = false" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 dark:border-gray-500 shadow-sm px-6 py-3 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yt sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors duration-200">
                            キャンセル
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection