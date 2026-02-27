<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChannelController extends Controller
{
    public function index(Request $request)
    {
        $userId = \Illuminate\Support\Facades\Auth::id();
        $query = \App\Models\Channel::with('category')->where('user_id', $userId);

        // Search
        if ($request->filled('search')) {
            $searchTerm = $request->get('search');
            $query->where('title', 'like', '%' . $searchTerm . '%');
        }

        // Sorting
        $sort = $request->get('sort', 'last_video_at');
        $direction = $request->get('direction', 'desc');

        if (in_array($sort, ['last_video_at', 'subscriber_count'])) {
            $query->orderBy($sort, $direction);
        }

        $channels = $query->get();
        $categories = \App\Models\Category::where('user_id', $userId)->get();
        $search = $request->get('search');

        return view('channels.index', compact('channels', 'categories', 'sort', 'direction', 'search'));
    }

    public function update(Request $request, \App\Models\Channel $channel)
    {
        if ($channel->user_id !== \Illuminate\Support\Facades\Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'priority' => 'required|integer|in:1,2,3',
            'memo' => 'nullable|string',
        ]);

        $channel->update($validated);

        return redirect()->route('channels.index')->with('success', 'チャンネル情報を更新しました。');
    }

    public function destroy(\App\Models\Channel $channel)
    {
        if ($channel->user_id !== \Illuminate\Support\Facades\Auth::id()) {
            abort(403);
        }

        $channel->delete();

        return redirect()->route('channels.index')->with('success', 'チャンネルを削除しました。');
    }

    public function sync()
    {
        $user = \Illuminate\Support\Facades\Auth::user();

        \App\Jobs\SyncYouTubeChannels::dispatch($user);

        return redirect()->route('channels.index')->with('success', 'YouTubeチャンネルの同期を開始しました。バックグラウンドで処理されます。');
    }
}
