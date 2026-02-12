<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketFormField extends Model
{
    protected $fillable = [
        'template_id',
        'key',
        'label',
        'type',
        'required',
        'options',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'required' => 'bool',
            'options' => 'array',
            'sort_order' => 'int',
        ];
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(TicketFormTemplate::class, 'template_id');
    }
}

