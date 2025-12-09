<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TouristGuide extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'profile_image',
        'experiences',
        'language_id',
        'years_of_experience',
        'driving_license_image',
        'license_expiry_date',
        'cv',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'license_expiry_date' => 'date',
        ];
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
