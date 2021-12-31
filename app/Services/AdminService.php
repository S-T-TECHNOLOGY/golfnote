<?php


namespace App\Services;


use App\Constants\Consts;
use App\Constants\NotificationType;
use App\Constants\ReservationStatus;
use App\Http\Resources\AdminEventCollection;
use App\Http\Resources\AdminGolfCollection;
use App\Http\Resources\AdminQuestionCollection;
use App\Http\Resources\AdminUserCollection;
use App\Http\Resources\NotificationResource;
use App\Http\Resources\UserEventReservationCollection;
use App\Http\Resources\UserReservationCollection;
use App\Jobs\SendNotificationReservationGolfSuccess;
use App\Models\Event;
use App\Models\Golf;
use App\Models\HoleImage;
use App\Models\Notification;
use App\Models\Question;
use App\Models\User;
use App\Models\UserEventReservation;
use App\Models\UserReservation;
use App\Utils\UploadUtil;

class AdminService
{
    public function getReservationGolf($params)
    {
        $limit = isset($params['limit']) ? $params['limit'] : Consts::LIMIT_DEFAULT;
        $key = isset($params['key']) ? $params['key'] : '';
        $reservations = UserReservation::where('status', ReservationStatus::PENDING_STATUS)
            ->when(!empty($key), function ($query) use ($key) {
                return $query->where('email', 'like', '%' . $key .'%');
            })
            ->with('golf')->orderBy('created_at', 'desc')->paginate($limit);

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
        $reservations = UserEventReservation::where('status', ReservationStatus::PENDING_STATUS)
            ->when(!empty($key), function ($query) use ($key) {
                return $query->where('email', 'like', '%' . $key .'%');
            })
            ->with('event')->orderBy('created_at', 'desc')->paginate($limit);

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
            'golf_id' => $id
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
            'event_id' => $id
        ];
        $notification = Notification::create($data);

        SendNotificationReservationGolfSuccess::dispatch($reservation->user_id, collect(new NotificationResource($notification))->toArray());
        return new \stdClass();
    }

    public function getGolfs($params)
    {
        $limit = isset($params['limit']) ? $params['limit'] : Consts::LIMIT_DEFAULT;
        $key = isset($params['key']) ? $params['key'] : '';
        $golfs = Golf::when(!empty($key), function ($query) use ($key) {
                return $query->where('name', 'like', '%' . $key .'%');
            })->orderBy('created_at', 'desc')->paginate($limit);
        return new AdminGolfCollection($golfs);
    }

    public function deleteGolf($id)
    {
        Golf::where('id', $id)->delete();
        return new \stdClass();
    }

    public function getEvents($params)
    {
        $limit = isset($params['limit']) ? $params['limit'] : Consts::LIMIT_DEFAULT;
        $key = isset($params['key']) ? $params['key'] : '';
        $now = date('Y-m-d H:i:s');
        $events = Event::when(!empty($key), function ($query) use ($key) {
            return $query->where('name', 'like', '%' . $key .'%');
        })->where('end_date', '>=', $now)->orderBy('id', 'desc')->paginate($limit);
        return new AdminEventCollection($events);
    }

    public function deleteEvent($id)
    {
        Event::where('id', $id)->delete();
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


    public function getQuestions($params)
    {
        $limit = isset($params['limit']) ? $params['limit'] : Consts::LIMIT_DEFAULT;
        $key = isset($params['key']) ? $params['key'] : '';
        $reservations = Question::when(!empty($key), function ($query) use ($key) {
                return $query->where('questions', 'like', '%' . $key .'%');
            })->orderBy('created_at', 'desc')->paginate($limit);

        return new AdminQuestionCollection($reservations);
    }

    public function createQuestion($param)
    {
        Question::create($param);
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
}