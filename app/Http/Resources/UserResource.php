<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'first_name'        => $this->first_name,
            'last_name'         => $this->last_name,
            'full_name'         => $this->full_name,
            'gender'            => $this->gender,
            'registered_by'     => $this->registered_by,
            'email'             => $this->email,
            'phone'             => $this->phone,
            'image'             => $this->getFirstMediaUrl('avatar') ?: null,
            'email_verified_at' => $this->email_verified_at,
        ];
    }
}
