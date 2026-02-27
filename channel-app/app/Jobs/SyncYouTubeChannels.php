<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\YouTubeService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SyncYouTubeChannels implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public User $user
    ) {}

    /**
     * Execute the job.
     */
    public function handle(YouTubeService $youTubeService): void
    {
        try {
            $syncedCount = $youTubeService->syncUserChannels($this->user);
            Log::info("User {$this->user->id}: Successfully synced {$syncedCount} channels.");
        } catch (\Exception $e) {
            Log::error("User {$this->user->id}: Failed to sync channels. Error: " . $e->getMessage());
        }
    }
}
