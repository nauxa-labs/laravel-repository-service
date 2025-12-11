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

    /*
    |--------------------------------------------------------------------------
    | Auto-Binding
    |--------------------------------------------------------------------------
    |
    | When enabled, the package will automatically bind repository and service
    | interfaces to their implementations. This eliminates the need to manually
    | register bindings in your AppServiceProvider.
    |
    | Note: Manual bindings in your AppServiceProvider will always take priority.
    |
    */

    'auto_binding' => [
        'enabled' => false,  // Set to true to enable auto-binding
        'repositories' => true,
        'services' => true,
    ],
];
