<?php


namespace App\Http\Controllers;


use App\Http\Requests\AdminLoginRequest;
use App\Http\Requests\ForgotPasswordRequest;
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

    public function register(RegisterRequest $request)
    {
        $user = $this->authService->register($request->all());
        return $this->sendResponse($user);
    }

    public function login(LoginRequest $request)
    {
        $data = $this->authService->login($request->all());
        return $this->sendResponse($data);
    }

    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $data = $this->authService->forgotPassword($request->all());
        return $this->sendResponse($data);
    }

    public function loginAdmin(AdminLoginRequest $request)
    {
        $data = $this->authService->loginAdmin($request->all());
        return $this->sendResponse($data);
    }

}