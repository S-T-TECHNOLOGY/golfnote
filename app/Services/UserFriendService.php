<?php


namespace App\Services;


use App\Constants\UserAddFriendStatus;
use App\Errors\UserFriendErrorCode;
use App\Exceptions\BusinessException;
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
        if ($requestFriend->status == UserAddFriendStatus::PENDING_STATUS) {
            throw new BusinessException('Bạn đã gửi yêu cầu kết bạn cho người này', UserFriendErrorCode::USER_ADDED_REQUEST);
        }

        if ($requestFriend->status == UserAddFriendStatus::ACCEPTED_STATUS) {
            throw new BusinessException('Bạn và người đó là bạn bè', UserFriendErrorCode::USER_IS_FRIEND);
        }

        $params['status'] = UserAddFriendStatus::PENDING_STATUS;

        UserRequestFriend::create($params);
        return new \stdClass();
    }


    public function acceptRequest($params)
    {
        $requestAddFriend = $this->getAddFriendRequest($params);
        $requestAddFriend->status = UserAddFriendStatus::ACCEPTED_STATUS;
        $requestAddFriend->save();
        return new \stdClass();
    }

    public function cancelRequest($params)
    {
        $requestAddFriend = $this->getAddFriendRequestToCancel($params);
        $requestAddFriend->delete();
        return new \stdClass();
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