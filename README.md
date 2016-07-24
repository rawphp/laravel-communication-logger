# Laravel Communication Provider

[![Build Status](https://travis-ci.org/rawphp/laravel-communication-logger.svg?branch=master)](https://travis-ci.org/rawphp/laravel-communication-logger)

## 1. Install

As always, we need to pull in some dependencies through Composer.

    composer require rawphp/laravel-communication-logger

## 2. Register Provider

    RawPHP\LaravelCommunicationLogger\CommunicationLoggerProvider::class,
    
## 3. Register Middleware

Add the CommunicationLog middleware to the Kernel middleware array. This allows all requests and responses to be logged.

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
            CommunicationsLog::class,
        ],
    ];

## 3. Publish Config

    php artisan vendor:publish --provider="RawPHP\LaravelCommunicationLogger\CommunicationLoggerProvider"
