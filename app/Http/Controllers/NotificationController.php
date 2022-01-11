<?php

namespace App\Http\Controllers;

use App\Constants\NotificationType;
use App\Constants\SettingType;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use JWTAuth;

class NotificationController extends AppBaseController
{
    protected $notificationService;
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function getAll(Request $request)
    {
        $params = $request->all();
        $user = JWTAuth::user();
        $notifications = $this->notificationService->getAll($params, $user);
        return $this->sendResponse($notifications);
    }

    public function settingNotification()
    {
        $user = JWTAuth::user();
        $user->setting_notification = $user->setting_notification ? SettingType::INACTIVE : SettingType::ACTIVE;
        $user->save();
        $totalNotifications = Notification::where('user_id', $user->id)->where('type', NotificationType::RECEIVED_REQUEST_FRIEND)->where('is_read', 0)->count();
        $user->notification_unread = $totalNotifications;
        return $this->sendResponse($user);
    }

    public function read($id)
    {
        $notification = Notification::where('id', $id)->first();
        $notification->is_read = 1;
        $notification->save();
        return $this->sendResponse(new \stdClass());
    }
}
