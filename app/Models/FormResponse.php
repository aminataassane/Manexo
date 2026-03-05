<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use App\Traits\HasPublicId;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class FormResponse extends Model
{
    use HasPublicId, BelongsToOrganization;

    public static string $publicIdPrefix = 'RSP';

    protected $fillable = [
        'public_id',
        'organization_id',
        'form_id',
        'user_id',
        'assignment_id',
        'form_version',
        'responses',
        'field_snapshot',
        'ticket_id',
        'ip_address',
        'respondent_name',
        'respondent_email',
        'base_fields',
        'submitted_from',
        'public_form_slug',
    ];

    protected function casts(): array
    {
        return [
            'responses' => 'array',
            'field_snapshot' => 'array',
            'base_fields' => 'array',
            'form_version' => 'int',
        ];
    }

    /**
     * Store an uploaded file for this response and return the relative path.
     */
    public static function storeUploadedFile(UploadedFile $file, int $orgId, int $responseId, string $fieldKey): string
    {
        $ext = $file->getClientOriginalExtension() ?: 'bin';
        $filename = $fieldKey . '-' . Str::random(12) . '.' . $ext;
        $dir = "form-responses/org-{$orgId}/response-{$responseId}";

        return $file->storeAs($dir, $filename, 'local');
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
