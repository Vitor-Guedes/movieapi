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
    ]
];