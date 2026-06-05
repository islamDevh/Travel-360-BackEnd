<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class User extends Authenticatable implements JWTSubject, MustVerifyEmail, HasMedia
{
    use HasFactory, Notifiable, InteractsWithMedia;

    protected $fillable = [
        'first_name',
        'last_name',
        'full_name',
        'gender',
        'provider',
        'provider_id',
        'email',
        'phone',
        'password',
        'registered_by',
        'type', // user | guide | admin
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * check if user is verified based on registration method
     * @return bool
     */
    public function isVerified(): bool
    {
        return match ($this->registered_by) {
            'email' => !is_null($this->email_verified_at),
            'phone' => !is_null($this->phone_verified_at),
            default => false,
        };
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')->singleFile();
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }

    public function devices()
    {
        return $this->hasMany(Device::class);
    }
}
