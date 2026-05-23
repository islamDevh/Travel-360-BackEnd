<?php

namespace App\Services;

use App\Http\Resources\GuideAppResource;
use App\Models\GuideApp;
use Illuminate\Support\Facades\DB;

class GuideAppService
{
    /**
     * Store a new guide application and attach all uploaded media.
     */
    public function store(array $data): GuideAppResource
    {
        DB::beginTransaction();

        $guideApp = GuideApp::create([
            'user_id'                => $data['user_id'],
            'phone'                  => $data['phone'],
            'experience'             => $data['experience'],
            'years_experience'       => $data['years_experience'],
            'lang'                   => $data['lang'],
            'has_car'                => $data['has_car'] ?? null,
            'car_type'               => $data['car_type'] ?? null,
            'driving_license_expiry' => $data['driving_license_expiry'] ?? null,
            'car_number'             => $data['car_number'] ?? null,
            'country'                => $data['country'],
            'area'                   => $data['area'],
        ]);

        $guideApp->addMedia($data['image'])->toMediaCollection('image');
        $guideApp->addMedia($data['cv'])->toMediaCollection('cv');

        if (!empty($data['driving_license'])) {
            $guideApp->addMedia($data['driving_license'])->toMediaCollection('driving_license');
        }

        DB::commit();

        return new GuideAppResource($guideApp);
    }

    /**
     * Return all guide applications submitted by the authenticated user.
     */
    public function myApplications()
    {
        $applications = GuideApp::where('user_id', auth()->id())
            ->latest()
            ->get();

        return GuideAppResource::collection($applications);
    }
}
