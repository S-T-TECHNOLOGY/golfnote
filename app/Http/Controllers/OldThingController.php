<?php


namespace App\Http\Controllers;


use App\Services\OldThingService;
use Illuminate\Http\Request;

class OldThingController extends AppBaseController
{
    protected $oldThingService;
    public function __construct(OldThingService $oldThingService)
    {
        $this->oldThingService = $oldThingService;
    }

    public function getAll(Request $request)
    {
        $oldThings = $this->oldThingService->getAll($request->all());
        return $this->sendResponse($oldThings);
    }

    public function getDetail($id)
    {
        $oldThing = $this->oldThingService->getDetail($id);
        return $this->sendResponse($oldThing);
    }


}