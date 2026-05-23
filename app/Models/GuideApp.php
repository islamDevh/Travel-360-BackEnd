<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class GuideApp extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'phone',
        'experience',
        'years_experience',
        'lang',
        'has_car',
        'car_type',
        'driving_license_expiry',
        'car_number',
        'country',
        'area',
        'status',
        'rejected_reason',
    ];

    protected function casts(): array
    {
        return [
            'lang'                   => 'array',
            'has_car'                => 'boolean',
            'driving_license_expiry' => 'date',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')->singleFile();
        $this->addMediaCollection('cv')->singleFile();
        $this->addMediaCollection('driving_license')->singleFile();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
