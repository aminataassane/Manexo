<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormField extends Model
{
    protected $fillable = [
        'form_id',
        'type',
        'label',
        'key',
        'required',
        'sort_order',
        'configuration',
        'form_version',
    ];

    protected function casts(): array
    {
        return [
            'required' => 'bool',
            'sort_order' => 'int',
            'configuration' => 'array',
            'form_version' => 'int',
        ];
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    // ─── Configuration accessors ────────────────────────────────────

    public function getPlaceholderAttribute(): ?string
    {
        return $this->configuration['placeholder'] ?? null;
    }

    public function getHelpTextAttribute(): ?string
    {
        return $this->configuration['help_text'] ?? null;
    }

    public function getOptionsAttribute(): ?array
    {
        $opts = $this->configuration['options'] ?? null;
        return is_array($opts) ? $opts : null;
    }

    public function getAcceptAttribute(): ?string
    {
        return $this->configuration['accept'] ?? null;
    }
}
