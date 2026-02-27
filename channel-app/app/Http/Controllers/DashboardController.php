<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = \Illuminate\Support\Facades\Auth::id();

        $totalChannels = \App\Models\Channel::where('user_id', $userId)->count();
        $priorityOneChannels = \App\Models\Channel::where('user_id', $userId)->where('priority', 1)->count();

        // 1週間以内更新チャンネル数
        $recentChannels = \App\Models\Channel::where('user_id', $userId)
            ->where('last_video_at', '>=', now()->subWeek())
            ->count();

        return view('dashboard.index', compact('totalChannels', 'priorityOneChannels', 'recentChannels'));
    }
}
