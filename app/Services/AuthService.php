<?php


namespace App\Services;


use App\Constants\MailOtpType;
use App\Mail\SendOTP;
use App\Models\MailOtp;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthService
{
    public function register($params) {
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
}