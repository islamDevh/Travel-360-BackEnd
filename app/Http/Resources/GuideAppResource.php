<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GuideAppResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                     => $this->id,
            'user_id'                => $this->user_id,
            'phone'                  => $this->phone,
            'image'                  => $this->getFirstMediaUrl('image'),
            'experience'             => $this->experience,
            'years_experience'       => $this->years_experience,
            'cv'                     => $this->getFirstMediaUrl('cv'),
            'lang'                   => $this->lang,
            'has_car'                => $this->has_car,
            'car_type'               => $this->car_type,
            'driving_license'        => $this->getFirstMediaUrl('driving_license'),
            'driving_license_expiry' => $this->driving_license_expiry?->format('Y-m-d'),
            'car_number'             => $this->car_number,
            'country'                => $this->country,
            'area'                   => $this->area,
            'status'                 => $this->status,
            'rejected_reason'        => $this->rejected_reason,
            'created_at'             => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
