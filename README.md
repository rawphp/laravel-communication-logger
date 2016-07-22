# Laravel Communication Provider

## 1. Install

As always, we need to pull in some dependencies through Composer.

    composer require rawphp/laravel-communication-logger

## 2. Register Provider

    RawPHP\LaravelCommunicationLogger\CommunicationLoggerProvider::class,

## 3. Publish Config

    php artisan vendor:publish --provider="RawPHP\LaravelCommunicationLogger\CommunicationLoggerProvider"
    