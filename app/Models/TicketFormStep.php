<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TicketFormStep extends Model
{
    protected $fillable = [
        'template_id',
        'number',
        'title',
        'description',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'number' => 'int',
            'sort_order' => 'int',
        ];
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(TicketFormTemplate::class, 'template_id');
    }

    public function fields(): HasMany
    {
        return $this->hasMany(TicketFormField::class, 'step_id')->orderBy('sort_order')->orderBy('id');
    }
}

