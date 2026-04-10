<?php

return [

    'backend' => [
        'url'     => env('BACKEND_URL', 'http://localhost:8080'),
        'timeout' => env('BACKEND_TIMEOUT', 10),
    ],

    // Add additional downstream services here:
    // 'notifications' => [
    //     'url'     => env('NOTIFICATIONS_URL'),
    //     'timeout' => env('NOTIFICATIONS_TIMEOUT', 5),
    // ],

];
