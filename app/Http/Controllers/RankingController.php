<?php

namespace App\Http\Controllers;

use App\Services\RankingService;
use Illuminate\Http\Request;
use JWTAuth;

class RankingController extends AppBaseController
{
    protected $rankingService;
    public function __construct(RankingService $rankingService)
    {
        $this->rankingService = $rankingService;
    }

    public function getRanking(Request $request)
    {
        $user = JWTAuth::user();
        $data = $this->rankingService->getRanking($request->all(), $user);
        return $this->sendResponse($data);
    }
}
