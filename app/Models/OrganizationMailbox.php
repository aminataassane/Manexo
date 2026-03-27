<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;

class OrganizationMailbox extends Model
{
    protected $fillable = [
        'organization_id',
        'email',
        'display_name',
        'imap_host',
        'imap_port',
        'imap_username',
        'imap_password',
        'imap_encryption',
        'imap_folder',
        'smtp_host',
        'smtp_port',
        'smtp_username',
        'smtp_password',
        'smtp_encryption',
        'default_category_id',
        'default_priority_id',
        'default_group_id',
        'is_active',
        'last_fetched_at',
        'last_fetched_uid',
        'last_error_at',
        'last_error_message',
    ];

    protected function casts(): array
    {
        return [
            'imap_password' => 'encrypted',
            'imap_port' => 'integer',
            'smtp_password' => 'encrypted',
            'smtp_port' => 'integer',
            'is_active' => 'boolean',
            'last_fetched_at' => 'datetime',
            'last_fetched_uid' => 'integer',
            'last_error_at' => 'datetime',
        ];
    }

    /**
     * SMTP is considered configured if at least the host is set.
     * Username/password fall back to IMAP credentials when empty.
     */
    public function hasSmtpConfig(): bool
    {
        return ! empty($this->smtp_host);
    }

    public function smtpHost(): string
    {
        return $this->smtp_host;
    }

    public function smtpPort(): int
    {
        return $this->smtp_port ?: 587;
    }

    public function smtpUsername(): string
    {
        return $this->smtp_username ?: $this->imap_username;
    }

    public function smtpPassword(): string
    {
        return $this->smtp_password ?: $this->imap_password;
    }

    public function smtpEncryption(): string
    {
        return $this->smtp_encryption ?: 'tls';
    }

    public function buildSmtpTransport(): EsmtpTransport
    {
        $tls = match ($this->smtpEncryption()) {
            'ssl', 'tls' => true,
            default => false,
        };

        $transport = new EsmtpTransport(
            host: $this->smtpHost(),
            port: $this->smtpPort(),
            tls: $tls,
        );

        $transport->setUsername($this->smtpUsername());
        $transport->setPassword($this->smtpPassword());

        return $transport;
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function defaultCategory(): BelongsTo
    {
        return $this->belongsTo(TicketCategory::class, 'default_category_id');
    }

    public function defaultPriority(): BelongsTo
    {
        return $this->belongsTo(TicketPriority::class, 'default_priority_id');
    }

    public function defaultGroup(): BelongsTo
    {
        return $this->belongsTo(TicketGroup::class, 'default_group_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(InboundEmailLog::class, 'organization_mailbox_id');
    }
}
