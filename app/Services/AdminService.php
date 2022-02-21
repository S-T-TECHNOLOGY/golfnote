<?php


namespace App\Services;


use App\Constants\ActiveStatus;
use App\Constants\Consts;
use App\Constants\NotificationType;
use App\Constants\ReservationStatus;
use App\Constants\UserScoreImageStatus;
use App\Errors\NewsErrorCode;
use App\Exceptions\BusinessException;
use App\Http\Resources\AdminEventCollection;
use App\Http\Resources\AdminEventResource;
use App\Http\Resources\AdminGolfCollection;
use App\Http\Resources\AdminGolfDetailResource;
use App\Http\Resources\AdminMarketCollection;
use App\Http\Resources\AdminMarketResource;
use App\Http\Resources\AdminNewsCollection;
use App\Http\Resources\AdminNewsResource;
use App\Http\Resources\AdminNotificationCollection;
use App\Http\Resources\AdminOldThingCollection;
use App\Http\Resources\AdminQuestionCollection;
use App\Http\Resources\AdminStoreCollection;
use App\Http\Resources\AdminUserCollection;
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
use App\Models\RoomPlayer;
use App\Models\Store;
use App\Models\User;
use App\Models\UserCheckIn;
use App\Models\UserEventReservation;
use App\Models\UserReservation;
use App\Models\UserScoreImage;
use App\Utils\Base64Utils;
use App\Utils\UploadUtil;

class AdminService
{
    public function getReservationGolf($params)
    {
        $limit = isset($params['limit']) ? $params['limit'] : Consts::LIMIT_DEFAULT;
        $key = isset($params['key']) ? $params['key'] : '';
        $status = isset($params['status']) ? $params['status'] : '';
        $reservations = UserReservation::when(!empty($key), function ($query) use ($key) {
                return $query->where('email', 'like', '%' . $key .'%');
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
                return $query->where('email', 'like', '%' . $key .'%');
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

        return $golf;
    }

    public function editGolf($params)
    {
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
        $scoreImages = UserScoreImage::where('status', UserScoreImageStatus::PENDING_STATUS)->with('user')->paginate($limit);

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

}