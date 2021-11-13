<?php

return [
    'push_url' => 'https://fcm.googleapis.com/fcm/send',
    'server_key' => env('FIREBASE_SERVER_KEY', null),
    'device' => [
        'ios' => 'ios',
        'android' => 'android'
    ],
    'sound' => 'default'
];