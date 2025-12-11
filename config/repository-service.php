<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Generator Paths
    |--------------------------------------------------------------------------
    |
    | These paths are used by the artisan commands when generating repository
    | and service classes. They are relative to the app/ directory.
    |
    */

    'paths' => [
        'repositories' => 'Repositories',
        'services' => 'Services',
        'models' => 'Models',
    ],

    /*
    |--------------------------------------------------------------------------
    | Class Suffixes
    |--------------------------------------------------------------------------
    |
    | These suffixes are appended to the generated class names.
    | For example: UserRepository (interface), UserRepositoryImplement (class)
    |
    */

    'suffixes' => [
        'interface' => '',
        'implementation' => 'Implement',
    ],
];
