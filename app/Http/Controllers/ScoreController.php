<?php

namespace App\Http\Controllers;

use App\Http\Requests\ScoreRequest;
use App\Services\ScoreService;
use JWTAuth;

class ScoreController extends AppBaseController
{
    protected $scoreService;
    public function __construct(ScoreService $scoreService)
    {
        $this->scoreService =  $scoreService;
    }

    public function calculateScore(ScoreRequest $request, $id)
    {
        $params = $request->all();
        $params['id'] = $id;
        $data = $this->scoreService->calculateScore($params);
        return $this->sendResponse($data);
    }

    public function history()
    {
        $user = JWTAuth::user();
        $data  =$this->scoreService->history($user);
        return $this->sendResponse($data);
    }
}
