<?php

namespace App\Http\Controllers;

use App\Constants\ActiveStatus;
use App\Http\Requests\UserChangePasswordRequest;
use App\Http\Requests\UserCreateClubRequest;
use App\Http\Requests\UserEditProfileRequest;
use App\Http\Requests\UserEventReservationRequest;
use App\Http\Requests\UserReservationRequest;
use App\Http\Requests\UserSellOldThingRequest;
use App\Http\Resources\UserProfileResource;
use App\Models\User;
use App\Models\UserSummary;
use App\Services\RoomService;
use App\Services\UserService;
use Illuminate\Http\Request;
use JWTAuth;

class UserController extends AppBaseController
{
    protected $userService;
    protected $roomService;

    public function __construct(UserService $userService, RoomService $roomService)
    {
        $this->userService = $userService;
        $this->roomService = $roomService;
    }

    public function getUser()
    {
        $user = JWTAuth::user();
        $rankingUsers = UserSummary::when(true, function ($query) {
            return $query->selectRaw('*, RANK () OVER ( ORDER BY handicap_score) as rank_no');
        })->get();
        $userRanking = collect($rankingUsers)->first(function ($item) use ($user) {
            return $item->user_id === $user->id;
        });
        $totalUser = User::where('active', ActiveStatus::ACTIVE)->pluck('id')->toArray();
        $user->total_user = sizeof($totalUser);
        $user->rank_no = empty($userRanking) ? 0 : $userRanking->rank_no;
        return $this->sendResponse(new UserProfileResource($user));
    }

    public function find(Request $request)
    {
        $user = JWTAuth::user();
        $params = $request->all();
        $params['user_id'] = $user->id;
        $users = $this->userService->find($params);
        return $this->sendResponse($users);
    }

    public function changePassword(UserChangePasswordRequest $request)
    {
        $user = JWTAuth::user();
        $data = $this->userService->changePassword($request->all(), $user);
        return $this->sendResponse($data);
    }

    public function reservationGolf(UserReservationRequest $request)
    {
        $user = JWTAuth::user();
        $params = $request->all();
        $params['user_id'] = $user->id;
        $data = $this->userService->reservationGolf($params);
        return $this->sendResponse($data);
    }

    public function reservationEvent(UserEventReservationRequest $request)
    {
        $user = JWTAuth::user();
        $params = $request->all();
        $params['user_id'] = $user->id;
        $data = $this->userService->reservationEvent($params);
        return $this->sendResponse($data);
    }

    public function sellOldThing(UserSellOldThingRequest $request)
    {
        $user = JWTAuth::user();
        $params = $request->all();
        $params['user_id'] = $user->id;
        $data = $this->userService->sellOldThing($params);
        return $this->sendResponse($data);
    }

    public function getRoomPlaying()
    {
        $user = JWTAuth::user();
        $room = $this->roomService->getRoomPlayingByUser($user->id);
        return $this->sendResponse($room);
    }

    public function createClub(UserCreateClubRequest $request)
    {
        $params = $request->all();
        $user = JWTAuth::user();
        $params['user_id'] = $user->id;
        $data = $this->userService->createClub($params);
        return $this->sendResponse($data);
    }

    public function logout()
    {
        $user = JWTAuth::user();
        $user->fcm_token = '';
        $user->save();
        JWTAuth::invalidate(JWTAuth::getToken());
        return $this->sendResponse(new \stdClass());
    }

    public function editProfile(UserEditProfileRequest $request)
    {
        $params = $request->only(['name', 'avatar', 'gender', 'phone', 'address']);
        $user = JWTAuth::user();
        $data = $this->userService->editProfile($params, $user);
        return $this->sendResponse($data);
    }

}
