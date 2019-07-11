<?php

return [
    'default' => env('CONNECTION_SERVICE', 'standard'),

    'services' => [
        'standard' => [
            'url' => env('CONNECTION_SERVICE_URL'),
            'basic_authorization' => env('CONNECTION_SERVICE_BASIC_AUTH'),
        ],
    ],
];
