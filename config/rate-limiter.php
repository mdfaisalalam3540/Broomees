<?php

return [
    'default' => env('RATE_LIMITER_DRIVER', 'cache'),

    'limits' => [
        'read' => [
            'max_attempts' => 120,
            'decay_minutes' => 1,
        ],
        'write' => [
            'max_attempts' => 30,
            'decay_minutes' => 1,
        ],
    ],

    'cache_driver' => env('RATE_LIMITER_CACHE_DRIVER', 'redis'),
];