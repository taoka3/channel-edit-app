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
     * 登録チャンネルを1ページ分(最大50件)同期する
     *
     * @param User $user
     * @param string|null $pageToken
     * @return array ['synced' => int, 'nextPageToken' => string|null]
     * @throws \Exception
     */
    public function syncUserChannelsPage(User $user, ?string $pageToken = null): array
    {
        if (!$user->access_token) {
            throw new \Exception("YouTubeへのアクセス権がありません（Token未設定）。");
        }

        $isApiKey = str_starts_with($user->access_token, 'AIza');
        $referer = config('app.url');

        $params = [
            'part' => 'snippet',
            'maxResults' => 50,
            'pageToken' => $pageToken,
        ];

        if ($isApiKey) {
            $myChannelId = env('YOUTUBE_MY_CHANNEL_ID');
            if (!$myChannelId) {
                throw new \Exception("APIキーを使用する場合、サブスクリプション(登録チャンネル)を取得するには .env に YOUTUBE_MY_CHANNEL_ID を設定し、かつYouTubeの設定で登録チャンネルを公開にしている必要があります。");
            }
            $params['channelId'] = $myChannelId;
            $params['key'] = $user->access_token;
            $response = Http::withHeaders(['Referer' => $referer])
                ->timeout(30)
                ->get('https://www.googleapis.com/youtube/v3/subscriptions', $params);
        } else {
            $params['mine'] = 'true';
            $response = Http::withToken($user->access_token)
                ->withHeaders(['Referer' => $referer])
                ->timeout(30)
                ->get('https://www.googleapis.com/youtube/v3/subscriptions', $params);
        }

        if ($response->failed()) {
            Log::error("User {$user->id}: YouTube API Error (subscriptions): " . $response->body());
            throw new \Exception("YouTube APIの取得に失敗しました: " . $response->json('error.message', ''));
        }

        $data = $response->json();
        $items = $data['items'] ?? [];
        $nextPageToken = $data['nextPageToken'] ?? null;
        $totalResults = $data['pageInfo']['totalResults'] ?? 'unknown';

        $syncedCount = 0;
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

                $syncedCount++;
            }
        }

        return [
            'synced' => $syncedCount,
            'nextPageToken' => $nextPageToken,
            'totalResults' => $totalResults
        ];
    }

    /**
     * @param User $user
     * @return int
     * @throws \Exception
     * @deprecated ジョブ分割方式（syncUserChannelsPage）の使用を推奨します
     */
    public function syncUserChannels(User $user): int
    {
        $totalSynced = 0;
        $pageToken = null;
        do {
            $result = $this->syncUserChannelsPage($user, $pageToken);
            $totalSynced += $result['synced'];
            $pageToken = $result['nextPageToken'];
        } while ($pageToken);

        return $totalSynced;
    }

    /**
     * 複数チャンネルの統計と最新動画を一括・並列で取得して返す
     */
    private function fetchBulkChannelDetails(array $channelIds, string $token): array
    {
        $isApiKey = str_starts_with($token, 'AIza');
        $params = [
            'part' => 'statistics,contentDetails',
            'id' => implode(',', $channelIds),
            'maxResults' => 50,
        ];

        $referer = config('app.url');

        if ($isApiKey) {
            $params['key'] = $token;
            $channelResponse = Http::withHeaders(['Referer' => $referer])
                ->timeout(30)
                ->get('https://www.googleapis.com/youtube/v3/channels', $params);
        } else {
            $channelResponse = Http::withToken($token)
                ->withHeaders(['Referer' => $referer])
                ->timeout(30)
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
                    'last_video_at' => null,
                ];

                $uploadsPlaylistId = $item['contentDetails']['relatedPlaylists']['uploads'] ?? null;
                if ($uploadsPlaylistId) {
                    $playlistMap[$chId] = $uploadsPlaylistId;
                }
            }
        }

        if (!empty($playlistMap)) {
            $responses = Http::pool(function (\Illuminate\Http\Client\Pool $pool) use ($playlistMap, $isApiKey, $token, $referer) {
                $reqs = [];
                foreach ($playlistMap as $chId => $playlistId) {
                    $pParams = [
                        'part' => 'snippet',
                        'playlistId' => $playlistId,
                        'maxResults' => 1,
                    ];

                    if ($isApiKey) {
                        $pParams['key'] = $token;
                        $reqs[] = $pool->as($chId)
                            ->withHeaders(['Referer' => $referer])
                            ->timeout(30)
                            ->get('https://www.googleapis.com/youtube/v3/playlistItems', $pParams);
                    } else {
                        $reqs[] = $pool->as($chId)
                            ->withToken($token)
                            ->withHeaders(['Referer' => $referer])
                            ->timeout(30)
                            ->get('https://www.googleapis.com/youtube/v3/playlistItems', $pParams);
                    }
                }
                return $reqs;
            });

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
