<?php

namespace App\Http\Controllers\API\GuideApp;

use App\Http\Controllers\API\BaseController;
use App\Http\Requests\API\StoreGuideAppRequest;
use App\Services\GuideAppService;

class GuideAppController extends BaseController
{
    public function __construct(protected GuideAppService $guideAppService)
    {
    }

    /**
     * Submit a new guide application.
     */
    public function store(StoreGuideAppRequest $request)
    {
        $data = $this->guideAppService->store($request->validated());
        return $this->successResponse($data, 'Application submitted successfully.', 201);
    }

    /**
     * List all guide applications for the authenticated user.
     */
    public function myApplications()
    {
        $data = $this->guideAppService->myApplications();
        return $this->successResponse($data);
    }
}
