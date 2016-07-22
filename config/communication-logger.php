<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Storage Driver
    |--------------------------------------------------------------------------
    |
    | Configure the storage type.
    |
    | Allowed types are 'file', 'database', 'memory'
    |
    */

    'driver' => env('COMMUNICATION_LOGGER_DRIVER', 'file'),

    'file' => [
        'storage-dir' => env('COMMUNICATION_LOGGER_STORAGE_PATH', storage_path('communication-logger')),
    ],

    'memory' => [

    ],

    'database' => [

        /*
        |--------------------------------------------------------------------------
        | Database Type
        |--------------------------------------------------------------------------
        |
        | Allowed types are 'mysql', 'mssql',
        */

        'type' => env('LOGGER_DATABASE_TYPE', 'mysql'),

        'name' => env('LOGGER_DATABASE_NAME', ''),

        'table' => env('LOGGER_TABLE_NAME', ''),

        'user' => env('LOGGER_DATABASE_USERNAME', ''),

        'password' => env('LOGGER_DATABASE_PASSWORD', ''),
    ],

];
