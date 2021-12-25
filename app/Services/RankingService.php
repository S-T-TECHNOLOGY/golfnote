<?php


namespace App\Services;


use App\Constants\Consts;
use App\Constants\RankingType;
use App\Constants\UserAddFriendStatus;
use App\Http\Resources\RankingCollection;
use App\Models\User;
use App\Models\UserRequestFriend;
use App\Models\UserSummary;


class RankingService
{
    public function getRanking($params, $user)
    {
        $limit = isset($params['limit']) ? $params['limit'] : Consts::LIMIT_DEFAULT;
        $type =  isset($params['type']) ? $params['type'] : RankingType::FRIEND_TYPE;
        $userIds = [];
        if ($type === RankingType::GENDER_TYPE) {
            $userIds = User::where('gender', $user->gender)->pluck('id')->toArray();
        }

        if ($type === RankingType::AREA_TYPE) {
            $userIds = User::where('address', $user->address)->pluck('id')->toArray();
        }

        if ($type === RankingType::FRIEND_TYPE) {
            array_push($userIds, $user->id);
            $userFriends = UserRequestFriend::where(function ($query) use ($user) {
                return $query->where('sender_id', $user->id)
                            ->orWhere('received_id', $user->id);
            })->where('status', UserAddFriendStatus::ACCEPTED_STATUS)->get();
            foreach ($userFriends as $friend) {
                if ($friend->sender_id === $user->id) {
                    array_push($userIds, $friend->received_id);
                } else {
                    array_push($userIds, $friend->sender_id);
                }
            }
        }

        $rankingUsers = UserSummary::when(true, function ($query) {
            return $query->selectRaw('*, RANK () OVER ( ORDER BY handicap_score) rank_no');
        })->when(sizeof($userIds), function ($query) use ($userIds) {
            return $query->whereIn('user_id', $userIds);
        })->with('user')->paginate($limit);

        return new RankingCollection($rankingUsers);
    }
}