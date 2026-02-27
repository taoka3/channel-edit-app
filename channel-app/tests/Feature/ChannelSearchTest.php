<?php

namespace Tests\Feature;

use App\Models\Channel;
use App\Models\User;
use App\Jobs\SyncYouTubeChannels;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ChannelSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_search_channels_by_title()
    {
        $user = User::factory()->create();

        Channel::factory()->create([
            'user_id' => $user->id,
            'title' => 'Laravel Tips',
            'youtube_channel_id' => 'UC1',
        ]);

        Channel::factory()->create([
            'user_id' => $user->id,
            'title' => 'Vue Mastery',
            'youtube_channel_id' => 'UC2',
        ]);

        $response = $this->actingAs($user)->get(route('channels.index', ['search' => 'Laravel']));

        $response->assertStatus(200);
        $response->assertSee('Laravel Tips');
        $response->assertDontSee('Vue Mastery');
    }

    public function test_sync_dispatches_job()
    {
        Queue::fake();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('channels.sync'));

        $response->assertRedirect();
        $response->assertSessionHas('success', 'YouTubeチャンネルの同期を開始しました。バックグラウンドで処理されます。');

        Queue::assertPushed(SyncYouTubeChannels::class, function ($job) use ($user) {
            return $job->user->id === $user->id;
        });
    }
}
