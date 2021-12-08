<?php


namespace App\Services;


use App\Constants\Consts;
use App\Constants\UserAddFriendStatus;
use App\Errors\AuthErrorCode;
use App\Errors\GolfCourseErrorCode;
use App\Exceptions\BusinessException;
use App\Http\Resources\UserCollection;
use App\Models\GolfCourse;
use App\Models\User;
use App\Models\UserRequestFriend;
use App\Models\UserReservation;
use Illuminate\Support\Facades\Hash;
use JWTAuth;

class UserService
{
    public function find($params)
    {
        $limit = isset($params['limit']) ? $params['limit'] : Consts::LIMIT_DEFAULT;
        $key = isset($params['key']) ? $params['key'] : '';
        $phones = isset($params['phones']) ? $params['phones'] : [];

        if (empty($key) && !sizeof($phones)) {
            return new \stdClass();
        }

        $users = User::when(!empty($key), function ($query) use ($key) {
            return $query->where(function ($query) use ($key) {
                return $query->where('account_name', 'like', '%' . $key . '%')
                            ->orWhere('phone', 'like', '%' . $key . '%');
            });
        })->when(sizeof($phones), function ($query) use ($phones) {
            return $query->whereIn('phone', $phones);
        })->where('active', 1)->where('id', '!=', $params['user_id'])->paginate($limit);

        if (sizeof($phones)) {
            $friendRequests = UserRequestFriend::where(function ($query) use ($params) {
                return $query->where('sender_id', $params['user_id'] )
                    ->orWhere('received_id', $params['user_id']);
            })->get();

            $users->map(function ($user) use ($friendRequests, $params) {
                $friendStatus = 0;
                $requestFriend = collect($friendRequests)->first(function ($item) use ($params, $user) {
                    return ($item['sender_id'] === $params['user_id'] && $item['received_id'] === $user->id)
                        || ($item['received_id'] === $params['user_id'] && $item['sender_id'] === $user->id);
                });
                if ($requestFriend) {
                    if ($requestFriend['status'] === UserAddFriendStatus::ACCEPTED_STATUS) {
                        $friendStatus = 3;
                    } else {
                        if ($requestFriend['sender_id'] === $params['user_id']) {
                            $friendStatus = 1;
                        } else {
                            $friendStatus = 2;
                        }
                    }
                }

                $user->friend_status = $friendStatus;
                return $user;
            });

        }

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

    public function reservationGolf($params)
    {
        $golf = GolfCourse::where('id', $params['golf_id'])->where('is_open', 1)->first();
        if (!$golf) {
            throw new BusinessException('Không tìm thấy sân golf', GolfCourseErrorCode::GOLF_COURSE_NOT_FOUND);
        }

        UserReservation::create($params);

        return new \stdClass();
    }
}