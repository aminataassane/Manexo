<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KbArticle extends Model
{
    use BelongsToOrganization;

    protected $fillable = [
        'organization_id',
        'kb_category_id',
        'created_by',
        'updated_by',
        'title',
        'slug',
        'content',
        'status',
        'visibility',
        'sort_order',
        'is_active',
        'keywords',
        'view_count',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'keywords' => 'array',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'view_count' => 'integer',
            'published_at' => 'datetime',
        ];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(KbCategory::class, 'kb_category_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published')->where('is_active', true);
    }

    public function scopePublicOnly(Builder $query): Builder
    {
        return $query->where('visibility', 'public');
    }

    public function excerpt(int $length = 150): string
    {
        $plain = strip_tags((string) ($this->content ?? ''));
        // Decode HTML entities then normalize non-breaking spaces and extra whitespace.
        $plain = html_entity_decode($plain, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $plain = str_replace("\xc2\xa0", ' ', $plain);
        $plain = preg_replace('/\s+/u', ' ', $plain) ?? $plain;

        return \Illuminate\Support\Str::limit(trim($plain), $length);
    }

    /**
     * Return content with only safe HTML tags allowed (XSS protection).
     */
    public function safeHtml(): string
    {
        $allowed = '<b><strong><i><em><u><br><p><ul><ol><li><h2><h3><h4><blockquote><a><hr>';

        return strip_tags($this->content ?? '', $allowed);
    }
}
