<?php


namespace App\Services;


use App\Constants\ActiveStatus;
use App\Constants\MailOtpType;
use App\Constants\NotificationType;
use App\Constants\UserSocialType;
use App\Errors\AuthErrorCode;
use App\Errors\AuthSocialErrorCode;
use App\Exceptions\BusinessException;
use App\Mail\ForgotPassword;
use App\Mail\SendOTP;
use App\Models\Admin;
use App\Models\MailOtp;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use JWTAuth;

class AuthService
{
    public function register($params)
    {
        $params['password'] = Hash::make($params['password']);
        $params['gender'] = isset($params['gender']) ? $params['gender'] : 0;
        $params['address'] = isset($params['gender']) ? $params['address'] : 'Việt Nam (Miền Bắc)';
        $params['avatar'] = '/avatar/default.jpeg';
        $params['active'] = ActiveStatus::ACTIVE;
        $user = User::create($params);
//        $code = Str::random(32);
//        $mailOtpParams = [
//            'user_id' => $user->id,
//            'code' => $code,
//            'type' => MailOtpType::TYPE_REGISTER
//        ];
//        MailOtp::create($mailOtpParams);
//        Mail::queue(new SendOTP($user->email, $code, MailOtpType::TYPE_REGISTER));
        return $user;
    }

    public function login($params)
    {
        $user = User::where('email', $params['email'])->first();
        if (!$user) {
            throw new BusinessException('Email không hợp lệ', AuthErrorCode::EMAIL_WRONG);
        }

        if (!$user->active) {
            throw new BusinessException('Tài khoản chưa được kích hoạt', AuthErrorCode::USER_NOT_ACTIVE);
        }

        $attempt = [
            'email' => $params['email'],
            'password' =>$params['password']
        ];

        $token = JWTAuth::attempt($attempt);
        if (!$token) {
            throw new BusinessException('Password không đúng', AuthErrorCode::PASSWORD_WRONG);
        }

        $user->fcm_token = $params['fcm_token'];
        $user->device = $params['device'];
        $user->save();
        $totalNotifications = Notification::where('user_id', $user->id)->where('type', '!=', NotificationType::RECEIVED_REQUEST_FRIEND)->where('is_read', 0)->count();
        $user->notification_unread = $totalNotifications;

        return [
            'access_token' => $token,
            'user' => $user
        ];
    }

    public function loginSocial($params)
    {
        $user = User::where('social_id', $params['social_id'])->where('social_type', $params['social_type'])->first();
        if (!$user) {
            $email = isset($params['email']) ? $params['email'] : '';
            $phone = isset($params['phone']) ? $params['phone'] : '';
            $name = isset($params['name']) ? $params['name'] : '';
            if (empty($email) || empty($phone) || empty($name)) {
                throw new BusinessException("Social not created", AuthSocialErrorCode::SOCIAL_ID_NOT_CREATE);
            }

            $userByEmail = User::where('email', $email)->first();
            if ($userByEmail) {
                throw new BusinessException("Email already exists", AuthSocialErrorCode::EMAIL_ALREADY_EXITS);
            }

            $params['password'] = Hash::make('');
            $params['account_name'] = '';
            $params['active'] = ActiveStatus::ACTIVE;
            $params['avatar'] = '/avatar/default.jpeg';
            $user = User::create($params);
        } else {
            if (!$user->active) {
                throw new BusinessException('Tài khoản chưa được kích hoạt', AuthErrorCode::USER_NOT_ACTIVE);
            }
            $user->fcm_token = $params['fcm_token'];
            $user->device = $params['device'];
            $user->save();
        }

        $token = JWTAuth::fromUser($user);
        $totalNotifications = Notification::where('user_id', $user->id)->where('type', '!=', NotificationType::RECEIVED_REQUEST_FRIEND)->where('is_read', 0)->count();
        $user->notification_unread = $totalNotifications;

        return [
            'access_token' => $token,
            'user' => $user
        ];


    }

    public function forgotPassword($params)
    {
        $email = $params['email'];
        $user = User::where('email', $email)->first();
        if (!$user) {
            throw new BusinessException('Email không đúng', AuthErrorCode::USER_NOT_FOUND);
        }
        $password = Str::random(8);
        $user->password = Hash::make($password);
        $user->save();
        Mail::queue(new ForgotPassword($email, $password));

        return new \stdClass();
    }

    public function loginAdmin($params)
    {

        Config::set('auth.defaults.guard', 'admins');
        Config::set('auth.defaults.passwords', 'admins');

        $attempt = [
            'email' => $params['email'],
            'password' =>$params['password']
        ];

        $token = JWTAuth::attempt($attempt);
        if (!$token) {
            throw new BusinessException('Password không đúng', AuthErrorCode::PASSWORD_WRONG);
        }
        $user = Admin::select('id', 'name', 'email', 'role', 'golf_id')->where('email', $params['email'])->first();

        return [
            'access_token' => $token,
            'user' => $user
        ];
    }
}