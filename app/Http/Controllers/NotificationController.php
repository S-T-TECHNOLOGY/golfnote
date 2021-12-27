<?php

namespace App\Http\Controllers;

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
}
