<?php

namespace App\Http\Controllers;

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
