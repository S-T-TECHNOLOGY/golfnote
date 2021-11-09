<?php


namespace App\Traists;


use App\Services\FCMService;

trait PushNotificationTraist
{
    public function pushMessage($deviceToken, array $notification, array $data)
    {
        $pushNotificationService = new FCMService();

        return $pushNotificationService->send($deviceToken, $notification, $data);
    }

    public function pushMultMessages(array $deviceTokens, array $notification, array $data)
    {
        $pushNotificationService = new FCMService();

        return $pushNotificationService->sendMultiple($deviceTokens, $notification, $data);
    }
}