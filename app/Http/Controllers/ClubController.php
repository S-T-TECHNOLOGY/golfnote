<?php

namespace App\Http\Controllers;

use App\Services\ClubService;
use Illuminate\Http\Request;

class ClubController extends AppBaseController
{
    protected $clubService;
    public function __construct(ClubService $clubService)
    {
        $this->clubService = $clubService;
    }

    public function getAll(Request $request)
    {
        $data = $this->clubService->getAll($request->all());
        return $this->sendResponse($data);
    }
}
