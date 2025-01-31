<?php

return [
    'swagger' => [
        'files' => [
            __DIR__ . '/../Console/Commands/GenerateSwagger.php',
            base_path('modules/User/src/') . '/Http/Controllers/V1/UserController.php',
            base_path('modules/User/src/') . '/Http/Controllers/V1/ReviewController.php',
            base_path('modules/Movie/src/') . '/Http/Controllers/V1/MovieController.php',
        ],

        'dist' => base_path('public') . '/swagger/swagger.json'
    ],

    'images' => [
        'google_cse_key' => env('GOOGLE_CSE_KEY', ''),
        'google_cse_url' => env('GOOGLE_CSE_URL', 'https://www.googleapis.com/customsearch/v1'),
        'google_cse_id' => env('GOOGLE_CSE_ID', ''),
    ]
];