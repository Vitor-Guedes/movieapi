<?php

return [
    'guards' => [
        'api' => [
            'driver' => 'jwt',
            'provider' => 'movie_users'
        ]
    ],

    'providers' => [
        'movie_users' => [
            'driver' => 'eloquent',
            'model' => \Modules\User\Models\User::class
        ]
    ]
];