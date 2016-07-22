<?php

namespace RawPHP\LaravelCommunicationLogger;

use Illuminate\Support\Facades\Facade;

/**
 * Class CommunicationLoggerFacade
 *
 * @package RawPHP\LaravelCommunicationLogger
 */
class CommunicationLoggerFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'communication-logger';
    }
}
