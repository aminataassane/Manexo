<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiscussionMessage extends Model
{
    protected $fillable = [
        'thread_id',
        'user_id',
        'body',
        'attachments',
        'meta',
    ];

    protected $casts = [
        'attachments' => 'array',
        'meta' => 'array',
    ];

    public function thread(): BelongsTo
    {
        return $this->belongsTo(DiscussionThread::class, 'thread_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

