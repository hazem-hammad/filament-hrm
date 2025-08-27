<?php

namespace App\Models;

use App\Enum\UserStatus;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class User extends Authenticatable implements HasMedia
{
    use HasApiTokens, HasFactory, InteractsWithMedia, Notifiable, softDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'country_code',
        'password',
        'status',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public static array $allowedSearchFields = ['name', 'email'];

    public static function getAllowedSearchFields(): array
    {
        return static::$allowedSearchFields;
    }

    public function isSuspended()
    {
        return $this->status == UserStatus::SUSPENDED->value;
    }

    public function isActive()
    {
        return $this->status == UserStatus::ACTIVE->value;
    }

    #[Scope]
    public function active(Builder $query): Builder
    {
        return $query->where('status', UserStatus::ACTIVE->value);
    }

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'status' => 'boolean',
    ];

    #[Scope]
    public function filter(Builder $query): Builder
    {
        $filterClass = str_replace('Models', 'Filters', get_class($this)) . 'Filters';

        return (new $filterClass(request: request(), builder: $query))->apply();
    }

    /**
     * Get FCM device tokens for this user (enhanced version)
     * Only returns active tokens that have been used recently
     */
    public function routeNotificationForFcm()
    {
        return $this->tokens
            ->whereNotNull('device_token')
            ->where('is_active', true)
            ->where('last_used_at', '>', now()->subDays(30))
            ->pluck('device_token')
            ->toArray();
    }

    /**
     * Get all notification tokens for this user
     */
    public function notificationTokens()
    {
        return $this->tokens->whereNotNull('device_token');
    }

    /**
     * Check if user has valid device tokens for notifications
     */
    public function hasValidDeviceTokens(): bool
    {
        return $this->tokens()
            ->whereNotNull('device_token')
            ->where('is_active', true)
            ->where('last_used_at', '>', now()->subDays(30))
            ->exists();
    }

    /**
     * Get notification preferences for this user
     */
    public function getNotificationPreferences(): array
    {
        return [
            'appointments' => true,
            'payments' => true,
            'messages' => true,
            'promotions' => false, // Default to false for marketing
        ];
    }

    /**
     * Check if user wants to receive specific notification type
     */
    public function wantsNotification(string $type): bool
    {
        $preferences = $this->getNotificationPreferences();
        return $preferences[$type] ?? false;
    }
}
