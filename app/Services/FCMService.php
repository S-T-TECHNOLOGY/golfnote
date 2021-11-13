<?php


namespace App\Services;


use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

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

    public function send($token, $data, $device)
    {
        $content = [
            "title" => "Golfnote",
            "body" => "Send Notification",
            'sound' => config('firebase.sound')
        ];

        if ($device === $this->apiConfig['device']['ios']) {
            $fields = [
                'to'   => $token,
                'notification' => $content,
                'data' => $data
            ];
        } else {
            $fields = [
                'to'   => $token,
                'data' => array_merge($data, $content)
            ];
        }

        return $this->sendPushNotification($fields);
    }

    public function sendMultiple($device_tokens, $data)
    {
        $content = [
            "title" => "Golfnote",
            "body" => "Send Notification",
            'sound' => config('firebase.sound')
        ];

        $fields = [
            'registration_ids' => $device_tokens,
            'data' => $data,
            'notification' => $content
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