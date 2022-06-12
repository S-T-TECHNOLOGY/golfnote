<?php


namespace App\Services;


use App\Constants\ActiveStatus;
use App\Constants\Consts;
use App\Constants\ReservationStatus;
use App\Constants\RoomStatus;
use App\Constants\UserAddFriendStatus;
use App\Errors\AuthErrorCode;
use App\Errors\ClubErrorCode;
use App\Errors\EventErrorCode;
use App\Errors\GolfCourseErrorCode;
use App\Errors\OldThingErrorCode;
use App\Errors\ReservationError;
use App\Errors\StoreErrorCode;
use App\Exceptions\BusinessException;
use App\Http\Resources\OldThingResource;
use App\Http\Resources\UserClubResource;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserReservationEventCollection;
use App\Http\Resources\UserReservationGolfCollection;
use App\Http\Resources\UserReservationGolfResource;
use App\Models\Event;
use App\Models\Golf;
use App\Models\OldThing;
use App\Models\Room;
use App\Models\Store;
use App\Models\User;
use App\Models\UserCheckIn;
use App\Models\UserClub;
use App\Models\UserEventReservation;
use App\Models\UserRequestFriend;
use App\Models\UserReservation;
use App\Models\UserScoreImage;
use App\Utils\Base64Utils;
use App\Utils\FormatTime;
use App\Utils\UploadUtil;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
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

        return new UserCollection($users);
    }

    public function findByPhone($params)
    {
        $users = $params['users'];
        $users = collect($users)->map(function ($item) {
            $item['phone'] = str_replace(' ', '', $item['phone']);
            return $item;
        })->map(function ($item){
            $item['phone'] =str_replace('+84', '0', $item['phone']);
            return $item;
        })->map(function ($item) {
            $item['phone'] =str_replace('+82', '0', $item['phone']);
            return $item;
        })->toArray();

        $phones = collect($users)->map(function ($item) {
           return $item['phone'];
        })->values()->toArray();
        $fundedUsers = User::when(sizeof($phones), function ($query) use ($phones) {
            return $query->whereIn('phone', $phones);
        })->where('active', 1)
            ->where('id', '!=', $params['user_id'])
            ->select('name', 'id', 'gender', 'avatar', 'phone', 'address')
            ->get();
        $friendRequests = UserRequestFriend::where(function ($query) use ($params) {
            return $query->where('sender_id', $params['user_id'] )
                ->orWhere('received_id', $params['user_id']);
        })->get();
        $fundedUsers->map(function ($user) use ($friendRequests, $params) {
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

        $users = collect($users)->map(function ($user) use ($fundedUsers) {
            $fundedUser = $fundedUsers->first(function ($item) use ($user){
                return $item['phone'] === $user['phone'];
            });
            if ($fundedUser) {
                return $fundedUser;
            }
            $user['id']  = 0;
            $user['avatar'] = '/avatar/default.jpeg';
            $user['address'] = '';
            $user['friend_status'] = -1;
            return $user;
        })->sortBy([
            ['friend_status', 'desc'],
        ])->toArray();

        return $users;

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
        $golf = Golf::where('id', $params['golf_id'])->where('is_open', 1)->first();
        if (!$golf) {
            throw new BusinessException('Không tìm thấy sân golf', GolfCourseErrorCode::GOLF_COURSE_NOT_FOUND);
        }

        UserReservation::create($params);

        return new \stdClass();
    }

    public function reservationEvent($params)
    {
        $now = date('Y-m-d H:i:s');
        $event = Event::where('end_date', '>=', $now)->where('id', $params['event_id'])->first();
        if (!$event) {
            throw new BusinessException('Không tìm thấy sự kiện', EventErrorCode::EVENT_NOT_FOUND);
        }

        UserEventReservation::create($params);

        return new \stdClass();
    }

    public function sellOldThing($params)
    {
        $images = [];
        foreach ($params['images'] as $image) {
            $urlImage = UploadUtil::saveBase64ImageToStorage($image, 'thing');
            array_push($images, $urlImage);
        }
        $params['image'] = json_encode($images);
        $params['quantity'] = 1;
        $params['quantity_remain'] = 1;
        $params['sale_off'] = 0;

        $oldThing = OldThing::create($params);

        return new OldThingResource($oldThing);
    }

    public function editOldThing($params)
    {
        $oldThing = OldThing::where('id', $params['id'])->where('user_id', $params['user_id'])->first();
        if (!$oldThing) {
            throw new BusinessException('Không tìm thấy chợ cũ', OldThingErrorCode::OLD_THING_NOT_FOUND);
        }
        $images = [];
        foreach ($params['images'] as $image) {
            if (Base64Utils::checkIsBase64($image)) {
                $image = UploadUtil::saveBase64ImageToStorage($image, 'thing');
            }
            array_push($images, $image);
        }
        $params['image'] = json_encode($images);

        $oldThing->update($params);

        return new OldThingResource($oldThing);
    }

    public function deleteOldThing($params)
    {
        OldThing::where('id', $params['id'])->where('user_id', $params['user_id'])->delete();

        return new \stdClass();
    }

    public function createClub($params)
    {
        $images = [];
        foreach ($params['images'] as $image) {
            $urlImage = UploadUtil::saveBase64ImageToStorage($image, 'club');
            array_push($images, $urlImage);
        }
        $params['images'] = json_encode($images);
        $club = UserClub::create($params);

        return new UserClubResource($club);
    }

    public function editClub($params) {
        $club = UserClub::where('id', $params['id'])->where('user_id', $params['user_id'])->first();
        if (!$club) {
            throw new BusinessException('Không tìm thấy club', ClubErrorCode::CLUB_NOT_FOUND);
        }
        $images = [];
        foreach ($params['images'] as $image) {
            if (Base64Utils::checkIsBase64($image)) {
                $image = UploadUtil::saveBase64ImageToStorage($image, 'club');
            }
            array_push($images, $image);
        }
        $params['images'] = json_encode($images);
        $club->update($params);

        return new UserClubResource($club);
    }

    public function deleteClub($params) {
        UserClub::where('id', $params['id'])->where('user_id', $params['user_id'])->delete();
        return new \stdClass();
    }

    public function editProfile($params, $user)
    {
        if (Base64Utils::checkIsBase64($params['avatar'])) {
            $params['avatar'] = UploadUtil::saveBase64ImageToStorage($params['avatar'], 'avatar');
        }
        $user->update($params);

        return $user;
    }

    public function uploadScoreImage($params, $user)
    {
        $params['image'] = UploadUtil::saveBase64ImageToStorage($params['image'], 'score');
        $params['user_id'] = $user->id;
        Room::where('id', $params['room_id'])->update(['status' => RoomStatus::HANDLE_SCORE]);
        UserScoreImage::create($params);
        return new \stdClass();
    }

    public function getReservationEventHistory($params, $user)
    {
        $key = isset($params['key']) ? $params['key'] : '';
        $limit = isset($params['limit']) ? $params['limit'] : Consts::LIMIT_DEFAULT;
        $fromDate = isset($params['from_date']) ? $params['from_date'] : '';
        $toDate = isset($params['to_date']) ? $params['to_date'] : '';
        $histories = DB::table('user_event_reservations')
            ->join('events', 'user_event_reservations.event_id', '=', 'events.id')
            ->where('user_event_reservations.user_id', $user->id)
            ->when(!empty($key), function ($query) use ($key) {
                return $query->where('events.name', 'like', '%' . $key .'%');
            })
            ->when(!empty($fromDate), function ($query) use ($fromDate) {
                return $query->whereDate('user_event_reservations.created_at', '>=', Carbon::parse(FormatTime::convertDate($fromDate)));
            })
            ->when(!empty($toDate), function ($query) use ($toDate) {
                return $query->whereDate('user_event_reservations.created_at', '<=', Carbon::parse(FormatTime::convertDate($toDate)));
            })
            ->select('user_event_reservations.id','user_event_reservations.status', 'user_event_reservations.created_at', 'events.name', 'events.address', 'events.id as event_id')
            ->orderBy('user_event_reservations.created_at', 'desc')
            ->paginate($limit);

        return new UserReservationEventCollection($histories);
    }

    public function getReservationGolfHistory($params, $user)
    {
        $key = isset($params['key']) ? $params['key'] : '';
        $limit = isset($params['limit']) ? $params['limit'] : Consts::LIMIT_DEFAULT;
        $fromDate = isset($params['from_date']) ? $params['from_date'] : '';
        $toDate = isset($params['to_date']) ? $params['to_date'] : '';
        $histories = DB::table('user_golf_reservations')
            ->join('golfs', 'user_golf_reservations.golf_id', '=', 'golfs.id')
            ->where('user_golf_reservations.user_id', $user->id)
            ->when(!empty($key), function ($query) use ($key) {
                return $query->where('golfs.name', 'like', '%' . $key .'%');
            })
            ->when(!empty($fromDate), function ($query) use ($fromDate) {
                return $query->whereDate('user_golf_reservations.created_at', '>=', Carbon::parse(FormatTime::convertDate($fromDate)));
            })
            ->when(!empty($toDate), function ($query) use ($toDate) {
                return $query->whereDate('user_golf_reservations.created_at', '<=', Carbon::parse(FormatTime::convertDate($toDate)));
            })
            ->select('user_golf_reservations.id','user_golf_reservations.status', 'user_golf_reservations.created_at', 'user_golf_reservations.date', 'golfs.name', 'golfs.address', 'golfs.id as golf_id')
            ->orderBy('user_golf_reservations.created_at', 'desc')
            ->paginate($limit);

        return new UserReservationGolfCollection($histories);
    }

    public function checkInStore($id, $user)
    {
        $store = Store::find($id);
        if (!$store) {
            throw new BusinessException('Không tìm thấy cửa hàng', StoreErrorCode::STORE_NOT_FOUND);
        }
        $store->check_in = $store->check_in + 1;
        $store->save();
        $params = [
            'store_id' => $id,
            'user_id' => $user->id
        ];
        UserCheckIn::create($params);

        return new \stdClass();
    }

    public function cancelReservationGolf($id, $user)
    {
        $reservationGolf = UserReservation::where('id', $id)->where('user_id', $user->id)->where('status', ReservationStatus::PENDING_STATUS)->first();
        if (!$reservationGolf) {
            throw new BusinessException('Không tìm thấy đăng ký sân golf', ReservationError::RESERVATION_NOT_FOUND);
        }

        $reservationGolf->status =ReservationStatus::CANCELED_STATUS;
        $reservationGolf->save();
        return new \stdClass();
    }

    public function cancelReservationEvent($id, $user)
    {
        $reservationEvent = UserEventReservation::where('id', $id)->where('user_id', $user->id)->where('status', ReservationStatus::PENDING_STATUS)->first();
        if (!$reservationEvent) {
            throw new BusinessException('Không tìm thấy đăng ký sự kiện', ReservationError::RESERVATION_NOT_FOUND);
        }

        $reservationEvent->status =ReservationStatus::CANCELED_STATUS;
        $reservationEvent->save();
        return new \stdClass();
    }

    public function deactive()
    {
        $user = JWTAuth::user();
        $user->active = ActiveStatus::INACTIVE;
        $user->fcm_token = '';
        $user->save();
        OldThing::where('user_id', $user->id)->where('quantity_remain', '>', 0)->update(['quantity_remain' => 0]);
        JWTAuth::invalidate(JWTAuth::getToken());
        return new \stdClass();
    }
}