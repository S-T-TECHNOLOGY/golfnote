<?php


namespace App\Services;


use App\Constants\MailOtpType;
use App\Errors\AuthErrorCode;
use App\Exceptions\BusinessException;
use App\Mail\SendOTP;
use App\Models\MailOtp;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use JWTAuth;

class AuthService
{
    public function register($params)
    {
        $params['password'] = Hash::make($params['password']);
        $user = User::create($params);
        $code = Str::random(32);
        $mailOtpParams = [
            'user_id' => $user->id,
            'code' => $code,
            'type' => MailOtpType::TYPE_REGISTER
        ];
        MailOtp::create($mailOtpParams);
        Mail::queue(new SendOTP($user->email, $code, MailOtpType::TYPE_REGISTER));
        return $user;
    }

    public function login($params)
    {
        $token = JWTAuth::attempt($params);
        if (!$token) {
            throw new BusinessException('Email hoặc password không đúng', AuthErrorCode::ACCOUNT_INVALID);
        }

        $user = JWTAuth::user();
        if (!$user->active) {
            throw new BusinessException('Tài khoản chưa được kích hoạt', AuthErrorCode::USER_NOT_ACTIVE);
        }

        return [
            'access_token' => $token,
            'user' => $user
        ];

    }
}