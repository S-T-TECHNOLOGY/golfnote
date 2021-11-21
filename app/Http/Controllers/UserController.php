<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserChangePasswordRequest;
use App\Services\UserService;
use Illuminate\Http\Request;
use JWTAuth;

class UserController extends AppBaseController
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function getUser()
    {
        $user = JWTAuth::user();
        return $this->sendResponse($user);
    }

    public function find(Request $request)
    {
        $user = JWTAuth::user();
        $params = $request->all();
        $params['user_id'] = $user->id;
        $users = $this->userService->find($params);
        return $this->sendResponse($users);
    }

    public function changePassword(UserChangePasswordRequest $request)
    {
        $user = JWTAuth::user();
        $data = $this->userService->changePassword($request->all(), $user);
        return $this->sendResponse($data);
    }
}
