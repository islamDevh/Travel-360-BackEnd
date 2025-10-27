<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): Jsonable|array
    {
        return [
            'id'                => $this->id,
            'first_name'        => $this->first_name,
            'last_name'         => $this->last_name,
            'full_name'         => $this->first_name . ' ' . $this->last_name,
            'gender'            => $this->gender,
            'registered_by'     => $this->registered_by,
            'email'             => $this->email,
            'phone'             => $this->phone,
            'image'             => $this->image ? asset(userImagePathFromPublic . $this->image) : null,
            'email_verified_at' => $this->email_verified_at,
        ];
    }
}
