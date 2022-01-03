<?php


namespace App\Services;


use App\Constants\Consts;
use App\Constants\NotificationType;
use App\Constants\UserAddFriendStatus;
use App\Constants\UserFriendStatus;
use App\Errors\UserFriendErrorCode;
use App\Exceptions\BusinessException;
use App\Http\Resources\NotificationResource;
use App\Http\Resources\UserCollection;
use App\Jobs\SendNotificationRequestFriend;
use App\Models\Notification;
use App\Models\User;
use App\Models\UserRequestFriend;

class UserFriendService
{
    public function addFriend($params)
    {
        $userFriend = User::where('id', $params['received_id'])
            ->where('id', '!=', $params['sender_id'])->where('active', 1)->first();
        if (!$userFriend) {
            throw new BusinessException('User không tồn tại', UserFriendErrorCode::USER_NOT_FOUND);
        }

        $requestFriend = UserRequestFriend::where(function ($query) use ($params) {
            return $query->where(function ($query) use ($params) {
                return $query->where('sender_id', $params['sender_id'])
                    ->where('received_id', $params['received_id']);
            })->orWhere(function ($query) use ($params) {
                return $query->where('received_id', $params['sender_id'])
                    ->where('sender_id', $params['received_id']);
            });
        })->first();
        if ($requestFriend) {
            if ($requestFriend->status == UserAddFriendStatus::PENDING_STATUS) {
                throw new BusinessException('Bạn đã gửi yêu cầu kết bạn cho người này', UserFriendErrorCode::USER_ADDED_REQUEST);
            }

            if ($requestFriend->status == UserAddFriendStatus::ACCEPTED_STATUS) {
                throw new BusinessException('Bạn và người đó là bạn bè', UserFriendErrorCode::USER_IS_FRIEND);
            }
        }

        $params['status'] = UserAddFriendStatus::PENDING_STATUS;

        $requestFriend = UserRequestFriend::create($params);
        $notificationParams = [
            'request_friend_id' => $requestFriend->id,
            'type' => NotificationType::RECEIVED_REQUEST_FRIEND,
            'user_id' => $params['received_id']
        ];
        $notification = Notification::create($notificationParams);
        SendNotificationRequestFriend::dispatch($params['received_id'], collect(new NotificationResource($notification))->toArray());

        return new \stdClass();
    }

    public function unFriend($params)
    {
        $userFriend = User::where('id', $params['received_id'])
            ->where('id', '!=', $params['sender_id'])->where('active', 1)->first();
        if (!$userFriend) {
            throw new BusinessException('User không tồn tại', UserFriendErrorCode::USER_NOT_FOUND);
        }

        UserRequestFriend::where(function ($query) use ($params) {
            return $query->where(function ($query) use ($params) {
                return $query->where('sender_id', $params['sender_id'])
                    ->where('received_id', $params['received_id']);
            })->orWhere(function ($query) use ($params) {
                return $query->where('received_id', $params['sender_id'])
                    ->where('sender_id', $params['received_id']);
            });
        })->where('status', UserAddFriendStatus::ACCEPTED_STATUS)->delete();

        return new \stdClass();
    }


    public function acceptRequest($params)
    {
        $requestAddFriend = $this->getAddFriendRequest($params);
        $requestAddFriend->status = UserAddFriendStatus::ACCEPTED_STATUS;
        $requestAddFriend->save();
        Notification::where('request_friend_id', $requestAddFriend->id)->delete();
        return new \stdClass();
    }

    public function rejectRequest($params)
    {
        $requestAddFriend = $this->getAddFriendRequest($params);
        Notification::where('request_friend_id', $requestAddFriend->id)->delete();
        $requestAddFriend->delete();
        return new \stdClass();
    }

    public function cancelRequest($params)
    {
        $requestAddFriend = $this->getAddFriendRequestToCancel($params);
        Notification::where('request_friend_id', $requestAddFriend->id)->delete();
        $requestAddFriend->delete();
        return new \stdClass();
    }

    public function getFriends($params, $user)
    {
        $limit = isset($params['limit']) ? $params['limit'] : Consts::LIMIT_DEFAULT;
        $key = isset($params['key']) ? $params['key'] : '';

        $requestFriend = UserRequestFriend::where(function ($query) use ($user) {
            return $query->where('sender_id', $user->id)->orWhere('received_id', $user->id);
        })->where('status', UserAddFriendStatus::ACCEPTED_STATUS)->get();

        $userIds = $requestFriend->map(function ($friend) use ($user){
           return $friend->sender_id ===  $user->id ? $friend->received_id : $friend->sender_id;
        })->toArray();

        $users = User::whereIn('id', $userIds)->when(!empty($key), function ($query) use ($key) {
            return $query->where('name', 'like', '%' . $key .'%');
        })->paginate($limit);

        $users->map(function ($item) {
            $item['friend_status'] = UserFriendStatus::FRIEND;
            return $item;
        });

        return new UserCollection($users);
    }

    public function getRequestFriends($params, $user)
    {
        $limit = isset($params['limit']) ? $params['limit'] : Consts::LIMIT_DEFAULT;
        $key = isset($params['key']) ? $params['key'] : '';

        $userIds = UserRequestFriend::where('received_id', $user->id)
            ->where('status', UserAddFriendStatus::PENDING_STATUS)->pluck('sender_id')->toArray();

        $users = User::whereIn('id', $userIds)->when(!empty($key), function ($query) use ($key) {
            return $query->where('name', 'like', '%' . $key .'%');
        })->paginate($limit);

        $users->map(function ($item) {
            $item['friend_status'] = UserFriendStatus::RECEIVED_REQUEST;
            return $item;
        });

        return new UserCollection($users);
    }
    private function getAddFriendRequest($params)
    {
        $requestAddFriend = UserRequestFriend::where('sender_id', $params['sender_id'])->where('received_id', $params['user_id'])
            ->where('status', UserAddFriendStatus::PENDING_STATUS)->first();
        if (!$requestAddFriend) {
            throw new BusinessException('Không tìm thấy yêu cầu kết bạn', UserFriendErrorCode::REQUEST_ADD_FRIEND_NOT_FOUND);
        }

        return $requestAddFriend;
    }
    private function getAddFriendRequestToCancel($params)
    {
        $requestAddFriend = UserRequestFriend::where('sender_id', $params['user_id'])->where('received_id', $params['received_id'])
            ->where('status', UserAddFriendStatus::PENDING_STATUS)->first();
        if (!$requestAddFriend) {
            throw new BusinessException('Không tìm thấy yêu cầu kết bạn', UserFriendErrorCode::REQUEST_ADD_FRIEND_NOT_FOUND);
        }

        return $requestAddFriend;
    }


}