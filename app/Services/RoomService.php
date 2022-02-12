<?php


namespace App\Services;


use App\Constants\Consts;
use App\Constants\RoomStatus;
use App\Errors\RoomErrorCode;
use App\Exceptions\BusinessException;
use App\Http\Resources\GolfResource;
use App\Jobs\SendNotificationCreateRoom;
use App\Models\Golf;
use App\Models\GolfHole;
use App\Models\Room;
use App\Models\RoomDraftScore;
use App\Models\RoomPlayer;
use App\Models\User;
use App\Traists\PushNotificationTraist;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RoomService
{
    use PushNotificationTraist;
    public function createRoom($params, $user)
    {
        $players = $params['players'];

        if (sizeof($players) > Consts::NUMBER_SLOT_MAX_ROOM - 1) {
            throw new BusinessException('Tối đa được 5 người chơi trong một phòng', RoomErrorCode::MAXIMUM_SLOT_IN_ROOM_ERROR);
        }

        $golfCourse = Golf::where('id', $params['golf_id'])->first();
        if (!$golfCourse) {
            throw new BusinessException('Sân golf không tìm thấy', RoomErrorCode::GOLF_NOT_FOUND);
        }

        $roomParams = [
            'owner_id' => $user->id,
            'golf_id' => $params['golf_id'],
            'status' => RoomStatus::GOING_ON_STATUS,
            'golf_courses' => json_encode($params['golf_courses'])
        ];

        if ($params['type'] == 0) {
            $roomParams['status'] = RoomStatus::HANDLE_SCORE_PENDING;
        }

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
            'golf' => new GolfResource($golfCourse),
            'owner' => [
              'user_id' => $user->id,
              'name' => $user->name,
              'phone' => $user->phone
            ],
            'golf_courses' => json_decode($room->golf_courses),
            'players' => $players
        ];

        $users = collect($users)->where('id', '!=', $user->id)->values();
        SendNotificationCreateRoom::dispatch($user, $users, $room, new GolfResource($golfCourse));
        return $data;
    }

    public function getRoomDetail($id)
    {
        $room = Room::find($id);
        if (!$room) {
            throw new BusinessException('Không tìm thấy phòng chơi', RoomErrorCode::ROOM_NOT_FOUND);
        }
        $ownerRoom = User::where('id', $room->owner_id)->select('id', 'name', 'phone')->first();
        $golf = Golf::select('name', 'id')->where('id', $room->golf_id)->first();
        $players = RoomPlayer::select('user_id', 'name', 'phone')->where('room_id', $id)->get();
        $userIdPlayers = collect($players)->filter(function ($player) {
            return $player->user_id > 0;
        })->pluck('user_id')->toArray();
        $userPlayers = User::whereIn('id', $userIdPlayers)->get();
        $roomPlayers = collect($players)->map(function ($player) use ($userPlayers) {
            if (!$player->user_id) {
                $player->avatar = '';
            } else {
                $user = $userPlayers->first(function ($item) use ($player) {
                   return $player->user_id == $item->id;
                });
                $player->name = $user->name;
                $player->phone = $user->phone;
                $player->avatar = $user->avatar;
            }
            return $player;
        })->toArray();
        $draftScore = RoomDraftScore::where('room_id', $id)->first();
        $holes = GolfHole::select('id', 'number_hole', 'standard')->where('type', 18)->get();
        $scores = [];
        if (!$draftScore) {
            $scores = collect($players)->map(function ($player) use ($holes) {
                $player['holes'] = $holes;
                return $player;
            })->toArray();
        }

        return [
            'owner_room' => $ownerRoom,
            'players' => $roomPlayers,
            'room_id' => $room->id,
            'golf' => $golf,
            'golf_courses' => json_decode($room->golf_courses),
            'time_updated' => empty($draftScore) ? Carbon::parse($room->created_at)->timestamp : Carbon::parse($draftScore->updated_at)->timestamp,
            'hole_current' => empty($draftScore) ? 0 : $draftScore->hole_current,
            'scores' => empty($draftScore) ? $scores : json_decode($draftScore->infor),
            'holes' => empty($draftScore) ? [] : json_decode($draftScore->holes),
        ];
    }

    public function getRoomPlayingByUser($userId)
    {
        $roomPlayings = Room::where('status', RoomStatus::GOING_ON_STATUS)->pluck('id')->toArray();
        $userRoomPlaying = RoomPlayer::whereIn('room_id', $roomPlayings)->where('user_id', $userId)->orderBy('id', 'desc')->first();
        if (!$userRoomPlaying) {
            return new \stdClass();
        }

        return $this->getRoomDetail($userRoomPlaying->room_id);
    }

}