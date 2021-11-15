<?php


namespace App\Services;


use App\Constants\Consts;
use App\Constants\RoomStatus;
use App\Errors\RoomErrorCode;
use App\Exceptions\BusinessException;
use App\Jobs\SendNotificationCreateRoom;
use App\Models\GolfCourse;
use App\Models\Room;
use App\Models\RoomPlayer;
use App\Models\User;
use App\Traists\PushNotificationTraist;

class RoomService
{
    use PushNotificationTraist;
    public function createRoom($params, $user)
    {
        $players = $params['players'];
        if (!sizeof($players)) {
            throw new BusinessException('Phòng chơi phải có ít nhất 2 người', RoomErrorCode::MIN_SLOT_IN_ROOM_ERROR);
        }

        if (sizeof($players) > Consts::NUMBER_SLOT_MAX_ROOM - 1) {
            throw new BusinessException('Tối đa được 5 người chơi trong một phòng', RoomErrorCode::MAXIMUM_SLOT_IN_ROOM_ERROR);
        }

        $golfCourse = GolfCourse::select('id', 'image', 'phone', 'address', 'description')->where('id', $params['golf_id'])->first();
        if (!$golfCourse) {
            throw new BusinessException('Sân golf không tìm thấy', RoomErrorCode::GOLF_NOT_FOUND);
        }

        $roomParams = [
            'owner_id' => $user->id,
            'golf_id' => $params['golf_id'],
            'status' => RoomStatus::GOING_ON_STATUS
        ];

        $room = Room::create($roomParams);
        $userIds = collect($players)->filter(function ($player) {
            return $player['user_id'] > 0;
        })->pluck('user_id')->all();
        array_unshift($userIds, $user->id);

        $guests = collect($players)->filter(function ($player) {
            return $player['user_id'] === 0;
        })->map(function ($player) use ($room) {
            $player['room_id'] = $room->id;
            return $player;
        })->all();

        $users = User::whereIn('id', $userIds)->get();
        $members = collect($users)->map(function ($user) use ($room) {
            $member = [
                'room_id' => $room->id,
                'user_id' => $user->id,
                'name' => $user->name,
                'phone' => $user->phone
            ];

            return $member;
        })->all();

        $players = array_merge($members, $guests);
        RoomPlayer::insert($players);

        $players = collect($players)->filter(function ($player) use ($user) {
            return $player['user_id'] !== $user->id;
        })->map(function ($player) {
            return collect($player)->only(['user_id', 'name', 'phone'])->all();
        })->values();

        $data = [
            'room_id' => $room->id,
            'golf' => $golfCourse,
            'owner' => [
              'user_id' => $user->id,
              'name' => $user->name,
              'phone' => $user->phone
            ],
            'players' => $players
        ];

        $users = collect($users)->where('id', '!=', $user->id)->values();
        SendNotificationCreateRoom::dispatch($user, $users, $room, $golfCourse);
        return $data;
    }

}