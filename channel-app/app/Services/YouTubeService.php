<?php

namespace App\Services;

use App\Models\User;
use App\Models\Channel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class YouTubeService
{
    /**
     * @param User $user
     * @return int
     * @throws \Exception
     */
    public function syncUserChannels(User $user): int
    {
        // 実際にはOAuth連携などでAccessTokenを取得、またはAPI Keyを利用。
        // MVPでは設定されたAPIキーまたは固定リストからチャンネル一覧を取得する想定、
        // あるいは $user->access_token を用いて subsciptions API を叩く実装をします。

        if (!$user->access_token) {
            throw new \Exception("YouTubeへのアクセス権がありません（Token未設定）。");
        }

        // タイムアウト対策として実行時間を延長 (可能であれば)
        set_time_limit(300);

        $channelsFetched = 0;
        $pageToken = null;
        $isApiKey = str_starts_with($user->access_token, 'AIza');

        $params = [
            'part' => 'snippet',
            'maxResults' => 50,
            'pageToken' => $pageToken,
        ];

        if ($isApiKey) {
            // APIキーの場合、mine=trueはエラーになります。公開されている特定のチャンネルIDの登録チャンネルしか取得できません。
            $myChannelId = env('YOUTUBE_MY_CHANNEL_ID');
            if (!$myChannelId) {
                throw new \Exception("APIキーを使用する場合、サブスクリプション(登録チャンネル)を取得するには .env に YOUTUBE_MY_CHANNEL_ID を設定し、かつYouTubeの設定で登録チャンネルを公開にしている必要があります。OAuthトークンであればマイチャンネルを直接取得可能です。");
            }
            $params['channelId'] = $myChannelId;
            $params['key'] = $user->access_token;
            $response = Http::timeout(10)->get('https://www.googleapis.com/youtube/v3/subscriptions', $params);
        } else {
            // OAuth 2.0 アクセストークンの場合
            $params['mine'] = 'true';
            $response = Http::withToken($user->access_token)
                ->timeout(10)
                ->get('https://www.googleapis.com/youtube/v3/subscriptions', $params);
        }

        if ($response->failed()) {
            Log::error('YouTube API Error (subscriptions): ' . $response->body());
            throw new \Exception("YouTube APIの取得に失敗しました: " . $response->json('error.message', ''));
        }

        $data = $response->json();
        $items = $data['items'] ?? [];

        $channelIds = [];
        $channelDetailsMap = [];

        foreach ($items as $item) {
            $snippet = $item['snippet'] ?? [];
            $resourceId = $snippet['resourceId'] ?? [];
            $youtubeChannelId = $resourceId['channelId'] ?? null;

            if (!$youtubeChannelId) continue;

            $channelIds[] = $youtubeChannelId;
            $channelDetailsMap[$youtubeChannelId] = [
                'title' => $snippet['title'] ?? 'Unknown',
                'thumbnail' => $snippet['thumbnails']['default']['url'] ?? null,
            ];
        }

        if (!empty($channelIds)) {
            // 各チャンネルの統計情報と最新動画を【一括】取得
            $bulkData = $this->fetchBulkChannelDetails($channelIds, $user->access_token);

            foreach ($channelIds as $id) {
                $details = $channelDetailsMap[$id];
                $apiData = $bulkData[$id] ?? [
                    'subscriber_count' => 0,
                    'video_count' => 0,
                    'last_video_at' => null,
                ];

                Channel::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'youtube_channel_id' => $id,
                    ],
                    [
                        'title' => $details['title'],
                        'thumbnail' => $details['thumbnail'],
                        'subscriber_count' => $apiData['subscriber_count'],
                        'video_count' => $apiData['video_count'],
                        'last_video_at' => $apiData['last_video_at'],
                    ]
                );

                $channelsFetched++;
            }
        }

        return $channelsFetched;
    }

    /**
     * 複数チャンネルの統計と最新動画を一括・並列で取得して返す
     *
     * @param array $channelIds
     * @param string $token
     * @return array
     */
    private function fetchBulkChannelDetails(array $channelIds, string $token): array
    {
        $isApiKey = str_starts_with($token, 'AIza');
        $params = [
            'part' => 'statistics,contentDetails',
            'id' => implode(',', $channelIds),
            'maxResults' => 50,
        ];

        // 1回のリクエストで最大50件のチャンネル統計を一括取得する
        if ($isApiKey) {
            $params['key'] = $token;
            $channelResponse = Http::timeout(10)->get('https://www.googleapis.com/youtube/v3/channels', $params);
        } else {
            $channelResponse = Http::withToken($token)
                ->timeout(10)
                ->get('https://www.googleapis.com/youtube/v3/channels', $params);
        }

        $results = [];
        $playlistMap = [];

        if ($channelResponse->successful()) {
            $cData = $channelResponse->json();
            $items = $cData['items'] ?? [];

            foreach ($items as $item) {
                $chId = $item['id'];
                $stat = $item['statistics'] ?? [];

                $results[$chId] = [
                    'subscriber_count' => $stat['subscriberCount'] ?? 0,
                    'video_count' => $stat['videoCount'] ?? 0,
                    'last_video_at' => null, // 後で並列取得
                ];

                // 動画一覧用プレイリストID
                $uploadsPlaylistId = $item['contentDetails']['relatedPlaylists']['uploads'] ?? null;
                if ($uploadsPlaylistId) {
                    $playlistMap[$chId] = $uploadsPlaylistId;
                }
            }
        }

        // プレイリストIDを元に最新動画を並列(非同期)で取得 (Http::poolを利用)
        if (!empty($playlistMap)) {
            $responses = Http::pool(function (\Illuminate\Http\Client\Pool $pool) use ($playlistMap, $isApiKey, $token) {
                $reqs = [];
                foreach ($playlistMap as $chId => $playlistId) {
                    $pParams = [
                        'part' => 'snippet',
                        'playlistId' => $playlistId,
                        'maxResults' => 1,
                    ];

                    if ($isApiKey) {
                        $pParams['key'] = $token;
                        $reqs[] = $pool->as($chId)->timeout(10)->get('https://www.googleapis.com/youtube/v3/playlistItems', $pParams);
                    } else {
                        $reqs[] = $pool->as($chId)->withToken($token)->timeout(10)->get('https://www.googleapis.com/youtube/v3/playlistItems', $pParams);
                    }
                }
                return $reqs;
            });

            // 並列リクエストのレスポンスを順次処理
            foreach ($responses as $chId => $response) {
                if ($response instanceof \Illuminate\Http\Client\Response && $response->successful()) {
                    $pData = $response->json();
                    if (!empty($pData['items'][0]['snippet']['publishedAt'])) {
                        $results[$chId]['last_video_at'] = Carbon::parse($pData['items'][0]['snippet']['publishedAt']);
                    }
                }
            }
        }

        return $results;
    }
}
