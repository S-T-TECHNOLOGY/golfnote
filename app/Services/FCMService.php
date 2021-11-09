<?php


namespace App\Services;


use GuzzleHttp\Client;

class FCMService
{


    private $apiConfig;

    public function __construct()
    {
        $this->apiConfig = [
            'url' => config('firebase.push_url'),
            'server_key' => config('firebase.server_key'),
            'device' => config('firebase.device')
        ];
    }

    public function send($token, $notification, $data)
    {
        if ($data['device'] === $this->apiConfig['device']['ios']) {
            $fields = [
                'to'   => $token,
                'notification' => $notification,
                'data' => $data
            ];
        } else {
            $fields = [
                'to'   => $token,
                'data' => array_merge($data, $notification)
            ];
        }

        return $this->sendPushNotification($fields);
    }

    public function sendMultiple($device_tokens, $notification, $data)
    {
        $fields = [
            'registration_ids' => $device_tokens,
            'data' => $data,
            'notification' => $notification
        ];

        return $this->sendPushNotification($fields);
    }


    private function sendPushNotification(array $fields)
    {
        $client = new Client([
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'key='. $this->apiConfig['server_key'],
            ]
        ]);
        $res = $client->post(
            $this->apiConfig['url'],
            ['body' => json_encode($fields)]
        );

        $res = json_decode($res->getBody());

        if ($res->failure) {
            Log::error("ERROR_PUSH_NOTIFICATION: ".$fields['to']);
        }

        return true;
    }
}