<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Channel>
 */
class ChannelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'youtube_channel_id' => $this->faker->unique()->slug,
            'title' => $this->faker->sentence(3),
            'thumbnail' => $this->faker->imageUrl(),
            'subscriber_count' => $this->faker->numberBetween(100, 10000000),
            'video_count' => $this->faker->numberBetween(10, 1000),
            'last_video_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'priority' => 2,
        ];
    }
}
