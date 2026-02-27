<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\YouTubeService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncYouTubeChannels implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @param User $user
     * @param string|null $pageToken 次のページを取得するためのトークン
     */
    public function __construct(
        public User $user,
        public ?string $pageToken = null
    ) {}

    /**
     * Execute the job.
     */
    public function handle(YouTubeService $youTubeService): void
    {
        try {
            $result = $youTubeService->syncUserChannelsPage($this->user, $this->pageToken);

            $syncedCount = $result['synced'];
            $nextPageToken = $result['nextPageToken'];
            $totalExpected = $result['totalResults'] ?? 'unknown';

            Log::info("User {$this->user->id}: Synced {$syncedCount} channels from page (Token: " . ($this->pageToken ?: 'Initial') . "). Total expected: {$totalExpected}");

            if ($nextPageToken) {
                Log::info("User {$this->user->id}: Dispatching next sync job with token: {$nextPageToken}");
                self::dispatch($this->user, $nextPageToken);
            } else {
                Log::info("User {$this->user->id}: YouTube synchronization completed.");
            }
        } catch (\Throwable $e) {
            Log::error("User {$this->user->id}: Failed to sync channels at page (Token: " . ($this->pageToken ?: 'Initial') . "). Error: " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine());
        }
    }
}
