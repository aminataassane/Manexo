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
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Illuminate\Notifications\Notifiable;

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
        $ttlSeconds = max(60, $expiresMinutes * 60);

        $key = $this->emailOtpRedisKey();
        $connection = (string) config('auth.email_otp_redis_connection', 'cache');

        Redis::connection($connection)->hmset($key, [
            'code_hash' => Hash::make($code),
            'expires_at' => now()->addMinutes($expiresMinutes)->timestamp,
            'attempts' => 0,
            'last_sent_at' => now()->timestamp,
        ]);

        // Expire the whole OTP payload automatically
        Redis::connection($connection)->expire($key, $ttlSeconds);

        $this->notify(new EmailVerificationOtpNotification(
            code: $code,
            expiresMinutes: $expiresMinutes,
        ));
    }

    /**
     * Redis key for email OTP.
     */
    public function emailOtpRedisKey(): string
    {
        // Spec prefix: otp:signup:user:{id}
        return "otp:signup:user:{$this->id}";
    }

    /**
     * Get the current email OTP payload from Redis.
     *
     * @return array{code_hash:string|null, expires_at:Carbon|null, attempts:int, last_sent_at:Carbon|null}|null
     */
    public function getEmailOtpPayload(): ?array
    {
        $connection = (string) config('auth.email_otp_redis_connection', 'cache');
        $data = Redis::connection($connection)->hgetall($this->emailOtpRedisKey());

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
     * Increment OTP attempts counter in Redis.
     */
    public function incrementEmailOtpAttempts(): int
    {
        $connection = (string) config('auth.email_otp_redis_connection', 'cache');

        return (int) Redis::connection($connection)->hincrby($this->emailOtpRedisKey(), 'attempts', 1);
    }

    /**
     * Delete OTP payload from Redis.
     */
    public function clearEmailOtpPayload(): void
    {
        $connection = (string) config('auth.email_otp_redis_connection', 'cache');
        Redis::connection($connection)->del($this->emailOtpRedisKey());
    }

    /**
     * Send the password reset notification (brand Manexo).
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordManexoNotification($token));
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
