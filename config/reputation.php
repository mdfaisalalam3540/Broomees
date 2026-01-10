<?php

return [
    'delete_threshold' => env('REPUTATION_DELETE_THRESHOLD', 10.0),

    'weights' => [
        'unique_friends' => 1.0,
        'shared_hobbies' => 0.5,
        'account_age_divisor' => 30,
        'account_age_max' => 3.0,
        'blocked_penalty' => 1.0,
    ],

    'recalculation' => [
        'batch_size' => 100,
        'schedule' => 'daily', // daily, hourly, weekly
    ],
];