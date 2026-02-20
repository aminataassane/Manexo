<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormResponse extends Model
{
    protected $fillable = [
        'form_id',
        'user_id',
        'assignment_id',
        'form_version',
        'responses',
        'field_snapshot',
        'ticket_id',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'responses' => 'array',
            'field_snapshot' => 'array',
            'form_version' => 'int',
        ];
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(FormAssignment::class, 'assignment_id');
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }
}
