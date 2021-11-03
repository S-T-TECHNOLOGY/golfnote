<?php

namespace App\Http\Controllers;

use App\Services\MarketService;
use Illuminate\Http\Request;

class MarketController extends AppBaseController
{
    protected $marketService;

    public function __construct(MarketService $marketService)
    {
        $this->marketService = $marketService;
    }

    public function getAll(Request $request)
    {
        $markets = $this->marketService->getAll($request->all());
        return $this->sendResponse($markets);
    }

    public function getDetail($id)
    {
        $market = $this->marketService->getDetail($id);
        return $this->sendResponse($market);
    }
}
