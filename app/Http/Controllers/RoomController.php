<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatRoomRequest;
use App\Services\RoomService;
use JWTAuth;

class RoomController extends AppBaseController
{
    protected $roomService;
    public function __construct(RoomService $roomService)
    {
        $this->roomService = $roomService;
    }

    public function createRoom(CreatRoomRequest $request)
    {
        $user = JWTAuth::user();
        $room = $this->roomService->createRoom($request->all(), $user);
        return $this->sendResponse($room);
    }
}
