<?php

namespace App\Http\Controllers;

use App\Services\ResortService;
use Illuminate\Http\Request;

class ResortController extends AppBaseController
{
    protected $resortService;

    public function __construct(ResortService $resortService)
    {
        $this->resortService = $resortService;
    }

    public function getAll(Request $request)
    {
        $markets = $this->resortService->getAll($request->all());
        return $this->sendResponse($markets);
    }

    public function getDetail($id)
    {
        $market = $this->resortService->getDetail($id);
        return $this->sendResponse($market);
    }
}
