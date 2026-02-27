<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'youtube_channel_id',
        'title',
        'thumbnail',
        'subscriber_count',
        'video_count',
        'last_video_at',
        'category_id',
        'priority',
        'memo',
    ];

    protected $casts = [
        'last_video_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
