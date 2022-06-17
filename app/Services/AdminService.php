<?php


namespace App\Services;


use App\Constants\ActiveStatus;
use App\Constants\Consts;
use App\Constants\NotificationType;
use App\Constants\ReservationStatus;
use App\Constants\RoomStatus;
use App\Constants\UserScoreImageStatus;
use App\Errors\AdminErrorCode;
use App\Errors\NewsErrorCode;
use App\Errors\RoomErrorCode;
use App\Exceptions\BusinessException;
use App\Http\Resources\AdminEventCollection;
use App\Http\Resources\AdminEventResource;
use App\Http\Resources\AdminGolfCollection;
use App\Http\Resources\AdminGolfDetailResource;
use App\Http\Resources\AdminGolfResource;
use App\Http\Resources\AdminMarketCollection;
use App\Http\Resources\AdminMarketResource;
use App\Http\Resources\AdminNewsCollection;
use App\Http\Resources\AdminNewsResource;
use App\Http\Resources\AdminNotificationCollection;
use App\Http\Resources\AdminOldThingCollection;
use App\Http\Resources\AdminQuestionCollection;
use App\Http\Resources\AdminStoreCollection;
use App\Http\Resources\AdminUserCollection;
use App\Http\Resources\GolfResource;
use App\Http\Resources\NotificationResource;
use App\Http\Resources\QuestionResource;
use App\Http\Resources\StoreCheckInCollection;
use App\Http\Resources\UserEventReservationCollection;
use App\Http\Resources\UserReservationCollection;
use App\Http\Resources\UserScoreImageCollection;
use App\Http\Resources\UserScoreImageResource;
use App\Jobs\SendNotificationAllUser;
use App\Jobs\SendNotificationReservationGolfSuccess;
use App\Models\AdminNotification;
use App\Models\Banner;
use App\Models\Event;
use App\Models\Golf;
use App\Models\HoleImage;
use App\Models\Market;
use App\Models\News;
use App\Models\Notification;
use App\Models\OldThing;
use App\Models\Question;
use App\Models\Room;
use App\Models\RoomDraftScore;
use App\Models\RoomPlayer;
use App\Models\RoomScore;
use App\Models\Store;
use App\Models\User;
use App\Models\UserCheckIn;
use App\Models\UserEventReservation;
use App\Models\UserReservation;
use App\Models\UserScoreImage;
use App\Utils\Base64Utils;
use App\Utils\UploadUtil;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminService
{
    public function getReservationGolf($params)
    {
        $limit = isset($params['limit']) ? $params['limit'] : Consts::LIMIT_DEFAULT;
        $key = isset($params['key']) ? $params['key'] : '';
        $status = isset($params['status']) ? $params['status'] : '';
        $reservations = UserReservation::when(!empty($key), function ($query) use ($key) {
            return $query->where(function ($q) use ($key) {
                return $q->where('email', 'like', '%' . $key .'%')->orWhere('user_name', 'like', '%' . $key .'%')->orWhere('phone', 'like', '%' . $key .'%');
            });
        })->when(strlen($status), function ($query) use ($status) {
            return $query->where('status', $status);
        })->with('golf')->orderBy('created_at', 'desc')->paginate($limit);

        return new UserReservationCollection($reservations);
    }

    public function getUsers($params)
    {
        $limit = isset($params['limit']) ? $params['limit'] : Consts::LIMIT_DEFAULT;
        $key = isset($params['key']) ? $params['key'] : '';
        $users = User::when(!empty($key), function ($query) use ($key) {
            return $query->where('name', 'like', '%' . $key .'%');
        })->paginate($limit);

        return new AdminUserCollection($users);
    }

    public function getReservationEvent($params)
    {
        $limit = isset($params['limit']) ? $params['limit'] : Consts::LIMIT_DEFAULT;
        $key = isset($params['key']) ? $params['key'] : '';
        $status = isset($params['status']) ? $params['status'] : '';
        $reservations = UserEventReservation::when(!empty($key), function ($query) use ($key) {
            return $query->where(function ($q) use ($key) {
                return $q->where('email', 'like', '%' . $key .'%')->orWhere('user_name', 'like', '%' . $key .'%')->orWhere('phone', 'like', '%' . $key .'%');
            });
        })->when(strlen($status), function ($query) use ($status) {
            return $query->where('status', $status);
        })->with('event')->orderBy('created_at', 'desc')->paginate($limit);

        return new UserEventReservationCollection($reservations);
    }

    public function reservationGolfSuccess($id)
    {
        $reservation = UserReservation::find($id);
        $reservation->status = ReservationStatus::SUCCESS_STATUS;
        $reservation->save();
        $data = [
            'type' => NotificationType::REGISTER_GOLF_SUCCESS,
            'user_id' => $reservation->user_id,
            'golf_id' => $reservation->golf_id
        ];
        $notification = Notification::create($data);

        SendNotificationReservationGolfSuccess::dispatch($reservation->user_id, collect(new NotificationResource($notification))->toArray());
        return new \stdClass();
    }

    public function reservationEventSuccess($id)
    {
        $reservation = UserEventReservation::find($id);
        $reservation->status = ReservationStatus::SUCCESS_STATUS;
        $reservation->save();
        $data = [
            'type' => NotificationType::REGISTER_EVENT_SUCCESS,
            'user_id' => $reservation->user_id,
            'event_id' => $reservation->event_id
        ];
        $notification = Notification::create($data);

        SendNotificationReservationGolfSuccess::dispatch($reservation->user_id, collect(new NotificationResource($notification))->toArray());
        return new \stdClass();
    }

    public function cancelReservationEvent($id)
    {
        $reservation = UserEventReservation::find($id);
        $reservation->status = ReservationStatus::CANCELED_STATUS;
        $reservation->save();
        return new \stdClass();
    }

    public function cancelReservationGolf($id)
    {
        $reservation = UserReservation::find($id);
        $reservation->status = ReservationStatus::CANCELED_STATUS;
        $reservation->save();
        return new \stdClass();
    }

    public function getGolfs($params)
    {
        $limit = isset($params['limit']) ? $params['limit'] : Consts::LIMIT_DEFAULT;
        $key = isset($params['key']) ? $params['key'] : '';
        $golfs = Golf::when(!empty($key), function ($query) use ($key) {
            return $query->where('name', 'like', '%' . $key .'%');
        })->where('is_open', ActiveStatus::ACTIVE)->orderBy('created_at', 'desc')->paginate($limit);
        return new AdminGolfCollection($golfs);
    }

    //manual score
    public function searchGolfs($params)
    {
        $keyword = isset($params['keyword']) ? $params['keyword'] : '';
        $golfs = Golf::when(!empty($keyword), function ($query) use ($keyword) {
            return $query->where('name', 'like', '%' . $keyword .'%');
        })->where('is_open', ActiveStatus::ACTIVE)->orderBy('created_at', 'desc')->get();
        foreach ($golfs as $golf) {
            $golf->golf_courses = json_decode($golf->golf_courses);
        }
        return [
            'data' => $golfs
        ];
    }
    public function searchUsers($params)
    {
        $keyword = isset($params['keyword']) ? $params['keyword'] : '';
        $users = User::when(!empty($keyword), function ($query) use ($keyword) {
            return $query->where('name', 'like', '%' . $keyword .'%')
                ->orWhere('account_name', 'like', '%' . $keyword .'%')
                ->orWhere('email', 'like', '%' . $keyword .'%')
                ->orWhere('phone', 'like', '%' . $keyword .'%');
        })->where('active', ActiveStatus::ACTIVE)->orderBy('created_at', 'desc')->get();
        return [
            'data' => $users
        ];
    }
    public function getHolesByGolfCourse($params)
    {
        $holeCourseA = HoleImage::select('number_hole', 'standard')->where('golf_id', $params['golf_id'])->where('course', $params['course_a'])->get();
        $holeCourseB = HoleImage::select('number_hole', 'standard')->where('golf_id', $params['golf_id'])->where('course', $params['course_b'])->get();
        $holeCourseB = $holeCourseB->map(function ($hole) {
            $hole['number_hole'] = $hole['number_hole'] + 9;
            return $hole;
        })->toArray();
        $golfHoles = array_merge($holeCourseA->toArray(), $holeCourseB);
        return [
            'holes' => $golfHoles
        ];
    }

    public function handleScoresManual($params) {
        DB::beginTransaction();
        if (count($params['scores']) > Consts::NUMBER_SLOT_MAX_ROOM - 1) {
            throw new BusinessException('Maximum of 5 players in a room.', RoomErrorCode::MAXIMUM_SLOT_IN_ROOM_ERROR);
        }
        //create room
        $roomParams = [
            'owner_id' => $params['owner_id'],
            'golf_id' => $params['golf_id'],
            'status' => RoomStatus::FINISHED_STATUS,
            'golf_courses' => json_encode($params['golf_courses'])
        ];
        $room = Room::create($roomParams);
        $draftScoreData = [
            'infor' => json_encode($params['scores']),
            'room_id' => $room->id,
            'hole_current' => 18
        ];
        RoomDraftScore::create($draftScoreData);
        foreach ($params['scores'] as $score) {
            $roomPlayerParams = [
                'room_id' => $room->id,
                'user_id' => $score['user_id'],
                'name' => $score['name'],
                'phone' => $score['phone'],
            ];
            RoomPlayer::create($roomPlayerParams);

            $score_total = collect($score['holes'])->sum('total');
            $scoreData = [
                'room_id' => $room->id,
                'user_id' => $score['user_id'],
                'name' => $score['name'],
                'phone' => $score['phone'],
                'infor' => json_encode($score['holes']),
                'score' => $score_total,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];
            RoomScore::create($scoreData);
        }
        DB::commit();
        return true;
    }

    public function getGolfDetail($id)
    {
        $golf = Golf::with('holes')->where('id', $id)->first();
        return new AdminGolfDetailResource($golf);
    }

    public function deleteGolf($id)
    {
        Golf::where('id', $id)->update([
            'is_open' => ActiveStatus::INACTIVE
        ]);
        return new \stdClass();
    }

    public function getEvents($params)
    {
        $limit = isset($params['limit']) ? $params['limit'] : Consts::LIMIT_DEFAULT;
        $key = isset($params['key']) ? $params['key'] : '';
        // $now = date('Y-m-d H:i:s');
        $events = Event::when(!empty($key), function ($query) use ($key) {
            return $query->where('name', 'like', '%' . $key .'%');
        })->where('status', ActiveStatus::ACTIVE)->orderBy('id', 'desc')->paginate($limit);
        return new AdminEventCollection($events);
    }

    public function getEventDetail($id)
    {
        $event = Event::find($id);
        return new AdminEventResource($event);
    }

    public function deleteEvent($id)
    {
        Event::where('id', $id)->update([
            'status' => ActiveStatus::INACTIVE
        ]);
        return new \stdClass();
    }

    public function createGolf($params)
    {
        DB::beginTransaction();
        $params['image'] = UploadUtil::saveBase64ImageToStorage($params['image'], 'golf');
        $params['is_open'] = 1;
        $params['number_hole'] = sizeof($params['golf_courses']) * 9 ;
        $courses = [];
        $holeImages = [];

        foreach ($params['golf_courses'] as  $course) {
            array_push($courses, $course['name']);
            $holes = collect($course['holes'])->map(function ($hole) use ($course) {
                $hole['course'] = $course['name'];
                return $hole;
            })->toArray();
            $holeImages = array_merge($holeImages, $holes);
        }

        $params['golf_courses'] = json_encode($courses);
        $golf = Golf::create($params);

        $holeImages = collect($holeImages)->map(function ($hole) use ($golf) {
            $hole['golf_id'] = $golf->id;
            return $hole;
        })->toArray();
        HoleImage::insert($holeImages);
        DB::commit();

        return $golf;
    }

    public function editGolf($params)
    {
        DB::beginTransaction();
        if (Base64Utils::checkIsBase64($params['image'])) {
            $params['image'] = UploadUtil::saveBase64ImageToStorage($params['image'], 'golf');
        }

        $params['is_open'] = 1;
        $params['number_hole'] = sizeof($params['golf_courses']) * 9 ;
        $courses = [];
        $holeImages = [];

        foreach ($params['golf_courses'] as  $course) {
            array_push($courses, $course['name']);
            $holes = collect($course['holes'])->map(function ($hole) use ($course) {
                $hole['course'] = $course['name'];
                return $hole;
            })->toArray();
            $holeImages = array_merge($holeImages, $holes);
        }

        $params['golf_courses'] = json_encode($courses);
        $golf = Golf::where('id', $params['id'])->update($params);

        $holeImages = collect($holeImages)->map(function ($hole) use ($params) {
            $hole['golf_id'] = $params['id'];
            return $hole;
        })->toArray();
        HoleImage::where('golf_id', $params['id'])->delete();
        HoleImage::insert($holeImages);
        DB::commit();
        return $golf;
    }


    public function getQuestions($params)
    {
        $limit = isset($params['limit']) ? $params['limit'] : Consts::LIMIT_DEFAULT;
        $key = isset($params['key']) ? $params['key'] : '';
        $questions = Question::when(!empty($key), function ($query) use ($key) {
            return $query->where('question', 'like', '%' . $key .'%');
        })->orderBy('created_at', 'desc')->paginate($limit);

        return new AdminQuestionCollection($questions);
    }

    public function getQuestionDetail($id)
    {
        $question = Question::find($id);
        return new QuestionResource($question);
    }

    public function createQuestion($param)
    {
        Question::create($param);
        return new \stdClass();
    }

    public function editQuestion($param)
    {
        Question::where('id', $param['id'])->update($param);
        return new \stdClass();
    }

    public function createEvent($params)
    {
        $params['image'] = UploadUtil::saveBase64ImageToStorage($params['image'], 'event');
        $params['quantity_remain'] = $params['quantity'];
        Event::create($params);
        return new \stdClass();
    }

    public function editEvent($params)
    {
        if (Base64Utils::checkIsBase64($params['image'])) {
            $params['image'] = UploadUtil::saveBase64ImageToStorage($params['image'], 'event');
        }
        $params['quantity_remain'] = $params['quantity'];
        Event::where('id', $params['id'])->update($params);

        return new \stdClass();
    }

    public function deleteQuestion($id)
    {
        Question::where('id', $id)->delete();
        return new \stdClass();
    }

    public function uploadImage($params)
    {
        $image = UploadUtil::saveBase64ImageToStorage($params['image'], $params['disk']);
        return [
            'image' => $image
        ];
    }

    public function getScoreImages($params)
    {
        $limit = isset($params['limit']) ? $params['limit'] : Consts::LIMIT_DEFAULT;
        $key = isset($params['key']) ? $params['key'] : '';
        $status = isset($params['status']) ? $params['status'] : '';
        $scoreImages = UserScoreImage::join('users', 'users.id', '=', 'user_score_images.user_id')
            ->select('user_score_images.*')
            ->where(function ($q) use ($key) {
                return $q->where('users.email', 'like', '%' . $key .'%')->orWhere('users.name', 'like', '%' . $key .'%')->orWhere('users.phone', 'like', '%' . $key .'%');
            });
        if ($status !== '') $scoreImages = $scoreImages->where('status', $status);
        $scoreImages = $scoreImages->with('user')->orderBy('status', 'ASC')->orderBy('created_at', 'DESC')->paginate($limit);

        return new UserScoreImageCollection($scoreImages);
    }

    public function getScoreImageDetail($id)
    {
        $scoreImage = UserScoreImage::where('id', $id)->with('room', 'user')->first();
        $golfCourses = json_decode($scoreImage->room->golf_courses);
        $holeCourseA = HoleImage::select('number_hole', 'standard')->where('golf_id', $scoreImage->room->golf_id)->where('course', $golfCourses[0])->get();
        $holeCourseB = HoleImage::select('number_hole', 'standard')->where('golf_id', $scoreImage->room->golf_id)->where('course', $golfCourses[1])->get();
        $holeCourseB = $holeCourseB->map(function ($hole) {
            $hole['number_hole'] = $hole['number_hole'] + 9;
            return $hole;
        })->toArray();
        $golfHoles = array_merge($holeCourseA->toArray(), $holeCourseB);
        $userPlayers = RoomPlayer::select('user_id', 'name', 'phone')->where('room_id', $scoreImage->room_id)->where('user_id', '>', 0)->get();
        $golf = Golf::select('id', 'name', 'address') ->where('id', $scoreImage->room->golf_id)->first();
        return [
            'id' => $scoreImage->id,
            'image' => $scoreImage->image,
            'users' => $userPlayers,
            'golf' => $golf,
            'holes' => $golfHoles
        ];
    }

    public function getScoreImageDetailEdit($id)
    {
        $scoreImage = UserScoreImage::where('id', $id)->with('room', 'user')->first();
        $golfCourses = json_decode($scoreImage->room->golf_courses);
        $holeCourseA = HoleImage::select('number_hole', 'standard')->where('golf_id', $scoreImage->room->golf_id)->where('course', $golfCourses[0])->get();
        $holeCourseB = HoleImage::select('number_hole', 'standard')->where('golf_id', $scoreImage->room->golf_id)->where('course', $golfCourses[1])->get();
        $holeCourseB = $holeCourseB->map(function ($hole) {
            $hole['number_hole'] = $hole['number_hole'] + 9;
            return $hole;
        })->toArray();
        $golfHoles = array_merge($holeCourseA->toArray(), $holeCourseB);
        foreach ($golfHoles as $hole) {
            $hole['total'] = 0;
            $hole['put'] = 0;
            $hole['short'] = 0;
            $hole['penalty'] = 0;
        }
        $userPlayers = RoomPlayer::select('user_id', 'name', 'phone')->where('room_id', $scoreImage->room_id)->where('user_id', '>', 0)->get();

        foreach ($userPlayers as $user) {
            $roomScore = RoomScore::where('user_id', $user->user_id)->where('room_id', $scoreImage->room_id)->first();
            if ($roomScore) {
                $user->holes = json_decode($roomScore->infor);
            } else {
                $user->holes = $golfHoles;
            }
        }
        $golf = Golf::select('id', 'name', 'address') ->where('id', $scoreImage->room->golf_id)->first();
        return [
            'id' => $scoreImage->id,
            'image' => $scoreImage->image,
            'users' => $userPlayers,
            'golf' => $golf,
        ];
    }

    public function handleEditScoreImage($params) {
        DB::beginTransaction();
        $draftScoreParams = [
            'infor' => json_encode($params['scores']),
            'room_id' => $params['id'],
            'hole_current' => 18
        ];
        RoomDraftScore::updateOrCreate(
            ['room_id' => $params['id']],
            $draftScoreParams);
        foreach ($params['scores'] as $score) {
            $roomPlayerParams = [
                'room_id' => $params['id'],
                'user_id' => $score['user_id'],
                'name' => $score['name'],
                'phone' => $score['phone'],
            ];
            RoomPlayer::updateOrCreate(
                ['room_id' => $params['id'], 'user_id' => $score['user_id']],
                $roomPlayerParams
            );

            $score_total = collect($score['holes'])->sum('total');
            $scoreData = [
                'room_id' => $params['id'],
                'user_id' => $score['user_id'],
                'name' => $score['name'],
                'phone' => $score['phone'],
                'infor' => json_encode($score['holes']),
                'score' => $score_total,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];
            RoomScore::updateOrCreate(
                ['room_id' => $params['id'], 'user_id' => $score['user_id']],
                $scoreData
            );
        }
        DB::commit();
        return true;
    }

    public function deleteScoreImage($id)
    {
        UserScoreImage::where('id', $id)->delete();
        return new \stdClass();
    }

    public function getMarkets($params)
    {
        $limit = isset($params['limit']) ? $params['limit'] : Consts::LIMIT_DEFAULT;
        $key = isset($params['key']) ? $params['key'] : '';
        $markets = Market::when(!empty($key), function ($query) use ($key) {
            return $query->where('name', 'like', '%' . $key .'%');
        })->orderBy('created_at', 'desc')->paginate($limit);

        return new AdminMarketCollection($markets);
    }

    public function getMarketDetail($id)
    {
        $market = Market::find($id);
        return new AdminMarketResource($market);
    }

    public function getOldMarkets($params)
    {
        $limit = isset($params['limit']) ? $params['limit'] : Consts::LIMIT_DEFAULT;
        $key = isset($params['key']) ? $params['key'] : '';
        $markets = OldThing::when(!empty($key), function ($query) use ($key) {
            return $query->where('name', 'like', '%' . $key .'%');
        })->with('user')->orderBy('created_at', 'desc')->paginate($limit);

        return new AdminOldThingCollection($markets);
    }

    public function createMarket($params)
    {
        $images = [];
        foreach ($params['images'] as $image) {
            $url = UploadUtil::saveBase64ImageToStorage($image, 'market');
            array_push($images, $url);
        }
        $params['image'] = json_encode($images);
        $params['quantity_remain'] = $params['quantity'];
        Market::create($params);

        return new \stdClass();
    }

    public function editMarket($params)
    {
        $images = [];
        foreach ($params['images'] as $image) {
            if (Base64Utils::checkIsBase64($image)) {
                $url = UploadUtil::saveBase64ImageToStorage($image, 'market');
                array_push($images, $url);
            } else {
                array_push($images, $image);
            }
        }
        $params['image'] = json_encode($images);
        $params['quantity_remain'] = $params['quantity'];
        unset($params['images']);
        Market::where('id', $params['id'])->update($params);

        return new \stdClass();
    }

    public function deleteMarket($id)
    {
        Market::where('id', $id)->delete();
        return new \stdClass();
    }

    public function deleteOldMarket($id)
    {
        OldThing::where('id', $id)->delete();
        return new \stdClass();
    }

    public function pushNotification($params)
    {
        $data = [
            'title' => $params['title'],
            'content' => $params['content'],
            'type' => NotificationType::OTHER
        ];
        $data['image'] = '';
        if (!empty($params['image']) && Base64Utils::checkIsBase64($params['image'])) {
            $data['image'] = UploadUtil::saveBase64ImageToStorage($params['image'], 'notification');
        }
        $users = User::whereNotNull('fcm_token')->get();
        AdminNotification::create($data);
        SendNotificationAllUser::dispatch($users, $data);

        return new \stdClass();
    }

    public function getAdminNotifications($params)
    {
        $limit = isset($params['limit']) ? $params['limit'] : Consts::LIMIT_DEFAULT;
        $key = isset($params['key']) ? $params['key'] : '';
        $notifications = AdminNotification::select('id', 'title', 'content', 'image')->when(!empty($key), function ($query) use ($key) {
            return $query->where('title', 'like', '%' . $key .'%');
        })->orderBy('created_at', 'desc')->paginate($limit);

        return new AdminNotificationCollection($notifications);
    }

    public function pushAllUserByTemplateNotification($id)
    {

        $notification = AdminNotification::find($id);
        $data = [
            'title' => $notification->title,
            'content' => $notification->content,
            'image' => $notification->image
        ];
        $users = User::whereNotNull('fcm_token')->get();
        SendNotificationAllUser::dispatch($users, $data);

        return new \stdClass();
    }

    public function deleteNotification($id)
    {
        AdminNotification::where('id', $id)->delete();
        return new \stdClass();
    }

    public function getBanner()
    {
        $banners = Banner::select('id', 'link', 'image', 'type')->get();
        return $banners;
    }

    public function deleteBanner($id)
    {
        Banner::where('id', $id)->delete();
        return new \stdClass();
    }

    public function getStores($params)
    {
        $limit = isset($params['limit']) ? $params['limit'] : Consts::LIMIT_DEFAULT;
        $key = isset($params['key']) ? $params['key'] : '';
        $stores = Store::when(!empty($key), function ($query) use ($key) {
            return $query->where('name', 'like', '%' . $key .'%');
        })->orderBy('created_at', 'desc')->paginate($limit);

        return new AdminStoreCollection($stores);
    }

    public function createStore($params)
    {
        Store::create($params);
        return new \stdClass();
    }

    public function deleteStore($id)
    {
        Store::where('id', $id)->delete();
        return new \stdClass();
    }

    public function getStoreDetail($id)
    {
        $store = Store::find($id);
        return $store;
    }

    public function getStoreCheckIn($params)
    {
        $limit = isset($params['limit']) ? $params['limit'] : Consts::LIMIT_DEFAULT;
        $histories = UserCheckIn::where('store_id', $params['id'])->orderBy('created_at', 'desc')->paginate($limit);
        return new StoreCheckInCollection($histories);
    }

    public function createNews($params)
    {
        $params['image'] = UploadUtil::saveBase64ImageToStorage($params['image'], 'news');
        News::create($params);
        return new \stdClass();
    }

    public function updateNews($params)
    {
        if (Base64Utils::checkIsBase64($params['image'])) {
            $params['image'] = UploadUtil::saveBase64ImageToStorage($params['image'], 'news');
        }

        News::where('id', $params['id'])->update($params);
        return new \stdClass();
    }

    public function deleteNews($id)
    {
        News::where('id', $id)->delete();
        return new \stdClass();
    }

    public function getNews($params)
    {
        $limit = isset($params['limit']) ? $params['limit'] : Consts::LIMIT_DEFAULT;
        $key = isset($params['key']) ? $params['key'] : '';
        $news = News::when(!empty($key), function ($query) use ($key) {
            return $query->where('title', 'like', '%' . $key .'%');
        })->orderBy('created_at', 'desc')->paginate($limit);

        return new AdminNewsCollection($news);
    }

    public function getNewsDetail($id)
    {

        $news = News::find($id);
        if (!$news) {
            throw new BusinessException('News not found',NewsErrorCode::NEWS_NOT_FOUND);
        }

        return new AdminNewsResource($news);
    }

    public function createUser($users)
    {
        $dataUsers = [];
        foreach ($users as $user) {
            $email = $user[0];
            $userByEmail = User::where('email', $email)->first();
            if ($userByEmail) {
                throw new BusinessException($email . " already exists", AdminErrorCode::USER_EMAIL_EXISTS);
            }
            $dataUser = [
                'email' => $email,
                'password' => Hash::make($user[1]),
                'name' => $user[2],
                'account_name' => $user[3],
                'phone' => $user[4],
                'address' => $user[5],
                'active' => ActiveStatus::ACTIVE,
                'avatar' => '/avatar/default.jpeg'
            ];
            array_push($dataUsers, $dataUser);
        }

        User::insert($dataUsers);

        return new \stdClass();
    }

}
