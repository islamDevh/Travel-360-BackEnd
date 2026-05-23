<?php

namespace App\Http\Controllers\API\Lookup;

use App\Http\Controllers\API\BaseController;
use App\Services\LookupService;

class LookupController extends BaseController
{
    public function __construct(protected LookupService $lookupService)
    {
    }

    /**
     * Return the list of available languages for multi-select.
     */
    public function languages()
    {
        $data = $this->lookupService->languages();
        return $this->successResponse($data);
    }

    /**
     * Return the list of countries.
     */
    public function countries()
    {
        $data = $this->lookupService->countries();
        return $this->successResponse($data);
    }

    /**
     * Return all Saudi Arabia regions with their cities.
     */
    public function saudiAreas()
    {
        $data = $this->lookupService->saudiAreas();
        return $this->successResponse($data);
    }
}
