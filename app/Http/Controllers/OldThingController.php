<?php


namespace App\Http\Controllers;


use App\Services\OldThingService;
use Illuminate\Http\Request;
use JWTAuth;

class OldThingController extends AppBaseController
{
    protected $oldThingService;
    public function __construct(OldThingService $oldThingService)
    {
        $this->oldThingService = $oldThingService;
    }

    public function getAll(Request $request)
    {
        $user = JWTAuth::user();
        $oldThings = $this->oldThingService->getAll($request->all(), $user);
        return $this->sendResponse($oldThings);
    }

    public function getDetail($id)
    {
        $oldThing = $this->oldThingService->getDetail($id);
        return $this->sendResponse($oldThing);
    }

    public function soldOut($id)
    {
        $user = JWTAuth::user();
        $oldThing = $this->oldThingService->soldOut($id,$user);
        return $this->sendResponse($oldThing);
    }


}