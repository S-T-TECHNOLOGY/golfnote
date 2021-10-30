<?php


namespace App\Http\Controllers;


use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;

class AuthController extends AppBaseController
{
    protected $authService;
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request) {
        $user = $this->authService->register($request->all());
        return $this->sendResponse($user);
    }

    public function login(LoginRequest $request) {
        $params = $request->only('email', 'password');
        $data = $this->authService->login($params);
        return $this->sendResponse($data);
    }

}