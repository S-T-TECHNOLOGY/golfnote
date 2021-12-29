<?php


namespace App\Services;


use App\Constants\Consts;
use App\Constants\NotificationType;
use App\Constants\ReservationStatus;
use App\Http\Resources\NotificationResource;
use App\Http\Resources\UserReservationCollection;
use App\Jobs\SendNotificationReservationGolfSuccess;
use App\Models\Notification;
use App\Models\UserReservation;

class AdminService
{
    public function getReservationGolf($params)
    {
        $limit = isset($params['limit']) ? $params['limit'] : Consts::LIMIT_DEFAULT;
        $reservations = UserReservation::where('status', ReservationStatus::PENDING_STATUS)->with('golf')->paginate($limit);

        return new UserReservationCollection($reservations);
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
}