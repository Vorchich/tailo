<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application for file storage.
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Below you may configure as many filesystem disks as necessary, and you
    | may even configure multiple disks for the same driver. Examples for
    | most supported storage drivers are configured here for reference.
    |
    | Supported drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
            'report' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ],
        'profile' => [
            'driver' => 'local',
            'root' => storage_path('app/public/profile'),
            'url' => env('APP_URL') . '/storage/profile',
            'visibility' => 'public',
            'throw' => false,
        ],

        'portfolio' => [
            'driver' => 'local',
            'root' => storage_path('app/public/portfolio'),
            'url' => env('APP_URL') . '/storage/portfolio',
            'visibility' => 'public',
            'throw' => false,
        ],

        'categories' => [
            'driver' => 'local',
            'root' => storage_path('app/public/categories'),
            'url' => env('APP_URL').'/storage/categories',
            'visibility' => 'public',
            'throw' => false,
        ],

        'sizes' => [
            'driver' => 'local',
            'root' => storage_path('app/public/sizes'),
            'url' => env('APP_URL').'/storage/sizes',
            'visibility' => 'public',
            'throw' => false,
        ],

        'books' => [
            'driver' => 'local',
            'root' => storage_path('app/public/books'),
            'url' => env('APP_URL').'/storage/books',
            'visibility' => 'public',
            'throw' => false,
        ],

        'messages' => [
            'driver' => 'local',
            'root' => storage_path('app/public/messages'),
            'url' => env('APP_URL').'/storage/messages',
            'visibility' => 'public',
            'throw' => false,
        ],

        'notepads' => [
            'driver' => 'local',
            'root' => storage_path('app/public/notepads'),
            'url' => env('APP_URL').'/storage/notepads',
            'visibility' => 'public',
            'throw' => false,
        ],

        'notepadFiles' => [
            'driver' => 'local',
            'root' => storage_path('app/public/notepadFiles'),
            'url' => env('APP_URL').'/storage/notepadFiles',
            'visibility' => 'public',
            'throw' => false,
        ],

        'post' => [
            'driver' => 'local',
            'root' => storage_path('app/public/post'),
            'url' => env('APP_URL').'/storage/post',
            'visibility' => 'public',
            'throw' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
            'report' => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
