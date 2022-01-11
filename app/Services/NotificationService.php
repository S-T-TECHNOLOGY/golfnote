<?php


namespace App\Services;


use App\Constants\Consts;
use App\Constants\NotificationType;
use App\Http\Resources\NotificationCollection;
use App\Models\Notification;

class NotificationService
{
    public function getAll($params, $user)
    {
        $limit = isset($params['limit']) ? $params['limit'] : Consts::LIMIT_DEFAULT;
        $notifications = Notification::where('user_id', $user->id)->with('golf', 'event')
            ->where('type', '!=', NotificationType::RECEIVED_REQUEST_FRIEND)
            ->orderBy('created_at', 'desc')
            ->paginate($limit);

        return new NotificationCollection($notifications);
    }
}