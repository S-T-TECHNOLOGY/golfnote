<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserAddFriendRequest;
use App\Services\UserFriendService;
use JWTAuth;

class UserFriendController extends AppBaseController
{
    protected $userFriendService;
    public function __construct(UserFriendService $friendService)
    {
        $this->userFriendService = $friendService;
    }

    public function addFriend(UserAddFriendRequest $request)
    {
        $params = $request->all();
        $user = JWTAuth::user();
        $params['sender_id'] = $user->id;
        $data = $this->userFriendService->addFriend($params);
        return $this->sendResponse($data);
    }

    public function acceptRequest($id)
    {
        $user = JWTAuth::user();
        $params = [
            'id' => $id,
            'user_id' => $user->id
        ];
        $data = $this->userFriendService->acceptRequest($params);
        return $this->sendResponse($data);
    }

    public function cancelRequest($id)
    {
        $user = JWTAuth::user();
        $params = [
            'id' => $id,
            'user_id' => $user->id
        ];
        $data = $this->userFriendService->cancelRequest($params);
        return $this->sendResponse($data);
    }
}
