<?php


namespace App\Traists;


use App\Services\FCMService;

trait PushNotificationTraist
{
    public function pushMessage($deviceToken, array $data, $device)
    {
        $pushNotificationService = new FCMService();

        return $pushNotificationService->send($deviceToken, $data, $device);
    }

    public function pushMultMessages(array $deviceTokens, array $data)
    {
        $pushNotificationService = new FCMService();

        return $pushNotificationService->sendMultiple($deviceTokens, $data);
    }
}