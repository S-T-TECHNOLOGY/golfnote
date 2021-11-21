<?php


namespace App\Services;


use App\Constants\Consts;
use App\Errors\AuthErrorCode;
use App\Exceptions\BusinessException;
use App\Http\Resources\UserCollection;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use JWTAuth;

class UserService
{
    public function find($params)
    {
        $limit = isset($params['limit']) ? $params['limit'] : Consts::LIMIT_DEFAULT;
        $key = isset($params['key']) ? $params['key'] : '';
        if (empty($key)) {
            return [];
        }
        $users = User::when(!empty($key), function ($query) use ($key) {
            return $query->where(function ($query) use ($key) {
                return $query->where('account_name', 'like', '%' . $key . '%')
                            ->orWhere('phone', 'like', '%' . $key . '%');
            });
        })->where('active', 1)->where('id', '!=', $params['user_id'])->paginate($limit);

        return new UserCollection($users);
    }

    public function changePassword($params, $user)
    {
        $checkOldPass = Hash::check($params['old_password'], $user->password);
        if (!$checkOldPass) {
            throw new BusinessException('Password hiện tại không chính xác', AuthErrorCode::PASSWORD_WRONG);
        }
        $user->password = Hash::make($params['new_password']);
        $user->save();

        $token = JWTAuth::fromUser($user);
        return [
            'access_token' => $token,
            'user' => $user
        ];
    }
}