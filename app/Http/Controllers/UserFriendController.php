<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserAcceptFriendRequest;
use App\Http\Requests\UserAddFriendRequest;
use App\Http\Requests\UserCancelFriendRequest;
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
        $params['received_id'] = $request->user_id;
        $params['request_content'] = $request->get('content');
        $user = JWTAuth::user();
        $params['sender_id'] = $user->id;
        $data = $this->userFriendService->addFriend($params);
        return $this->sendResponse($data);
    }

    public function acceptRequest(UserAcceptFriendRequest $request)
    {
        $user = JWTAuth::user();
        $params = [
            'sender_id' => $request->user_id,
            'user_id' => $user->id
        ];
        $data = $this->userFriendService->acceptRequest($params);
        return $this->sendResponse($data);
    }

    public function rejectRequest(UserAcceptFriendRequest $request)
    {
        $user = JWTAuth::user();
        $params = [
            'sender_id' => $request->user_id,
            'user_id' => $user->id
        ];
        $data = $this->userFriendService->rejectRequest($params);
        return $this->sendResponse($data);
    }

    public function cancelRequest(UserCancelFriendRequest $request)
    {
        $user = JWTAuth::user();
        $params = [
            'received_id' => $request->user_id,
            'user_id' => $user->id
        ];
        $data = $this->userFriendService->cancelRequest($params);
        return $this->sendResponse($data);
    }
}
