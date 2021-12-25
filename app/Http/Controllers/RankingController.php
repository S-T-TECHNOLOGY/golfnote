<?php

namespace App\Http\Controllers;

use App\Services\RankingService;
use Illuminate\Http\Request;

class RankingController extends AppBaseController
{
    protected $rankingService;
    public function __construct(RankingService $rankingService)
    {
        $this->rankingService = $rankingService;
    }

    public function getRanking(Request $request)
    {
        $data = $this->rankingService->getRanking($request->all());
        return $this->sendResponse($data);
    }
}
