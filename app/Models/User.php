<?php

namespace App\Models;

use App\Notifications\EmailVerificationOtpNotification;
use App\Notifications\ResetPasswordManexoNotification;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, MustVerifyEmailTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'mention_tag',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Send an OTP code to verify the user's email.
     */
    public function sendEmailVerificationNotification(): void
    {
        $code = (string) random_int(100000, 999999);
        $expiresMinutes = (int) config('auth.email_otp_expires_minutes', 10);

        $key = $this->emailOtpCacheKey();

        Cache::put($key, [
            'code_hash' => Hash::make($code),
            'expires_at' => now()->addMinutes($expiresMinutes)->timestamp,
            'attempts' => 0,
            'last_sent_at' => now()->timestamp,
        ], now()->addMinutes($expiresMinutes));

        $this->notify(new EmailVerificationOtpNotification(
            code: $code,
            expiresMinutes: $expiresMinutes,
        ));
    }

    /**
     * Cache key for email OTP.
     */
    public function emailOtpCacheKey(): string
    {
        return "otp:signup:user:{$this->id}";
    }

    /**
     * Get the current email OTP payload from cache.
     *
     * @return array{code_hash:string|null, expires_at:Carbon|null, attempts:int, last_sent_at:Carbon|null}|null
     */
    public function getEmailOtpPayload(): ?array
    {
        $data = Cache::get($this->emailOtpCacheKey());

        if (! is_array($data) || $data === []) {
            return null;
        }

        $expiresAtTs = isset($data['expires_at']) ? (int) $data['expires_at'] : null;
        $lastSentTs = isset($data['last_sent_at']) ? (int) $data['last_sent_at'] : null;

        return [
            'code_hash' => $data['code_hash'] ?? null,
            'expires_at' => $expiresAtTs ? Carbon::createFromTimestamp($expiresAtTs) : null,
            'attempts' => (int) ($data['attempts'] ?? 0),
            'last_sent_at' => $lastSentTs ? Carbon::createFromTimestamp($lastSentTs) : null,
        ];
    }

    /**
     * Increment OTP attempts counter in cache.
     */
    public function incrementEmailOtpAttempts(): int
    {
        $key = $this->emailOtpCacheKey();
        $data = Cache::get($key);

        if (! is_array($data)) {
            return 0;
        }

        $data['attempts'] = ((int) ($data['attempts'] ?? 0)) + 1;

        // Preserve remaining TTL
        $expiresAt = isset($data['expires_at']) ? Carbon::createFromTimestamp((int) $data['expires_at']) : now()->addMinutes(10);
        Cache::put($key, $data, $expiresAt);

        return $data['attempts'];
    }

    /**
     * Delete OTP payload from cache.
     */
    public function clearEmailOtpPayload(): void
    {
        Cache::forget($this->emailOtpCacheKey());
    }

    /**
     * Send the password reset notification (brand Manexo).
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordManexoNotification($token));
    }

    /**
     * Nom de tague pour les mentions @ dans les discussions (ex: @jdupont).
     * Si non défini, dérivé du prénom (slug).
     */
    public function getMentionTagAttribute(): string
    {
        $tag = $this->attributes['mention_tag'] ?? null;
        if ($tag !== null && $tag !== '') {
            return $tag;
        }
        $name = $this->attributes['name'] ?? '';
        $first = Str::before($name, ' ');
        $slug = Str::slug($first);
        return $slug !== '' ? $slug : 'user' . ($this->attributes['id'] ?? 0);
    }

    public function organizationMemberships(): HasMany
    {
        return $this->hasMany(OrganizationMembership::class);
    }

    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class, 'organization_memberships')
            ->withPivot(['role'])
            ->withTimestamps();
    }
}
