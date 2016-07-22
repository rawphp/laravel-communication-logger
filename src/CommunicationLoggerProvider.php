<?php

namespace RawPHP\LaravelCommunicationLogger;

use Exception;
use Illuminate\Support\ServiceProvider;
use Monolog\Logger as MonoLog;
use RawPHP\CommunicationLogger\Adapter\DatabaseAdapter;
use RawPHP\CommunicationLogger\Adapter\Factory\ConnectionFactory;
use RawPHP\CommunicationLogger\Adapter\FileAdapter;
use RawPHP\CommunicationLogger\Adapter\IAdapter;
use RawPHP\CommunicationLogger\Adapter\MemoryAdapter;
use RawPHP\CommunicationLogger\Factory\EventFactory;
use RawPHP\CommunicationLogger\Factory\IEventFactory;
use RawPHP\CommunicationLogger\Logger;
use RawPHP\CommunicationLogger\Util\IReader;
use RawPHP\CommunicationLogger\Util\IWriter;
use RawPHP\CommunicationLogger\Util\Reader;
use RawPHP\CommunicationLogger\Util\Writer;

/**
 * Class CommunicationLoggerProvider
 *
 * @package RawPHP\LaravelCommunicationLogger
 */
class CommunicationLoggerProvider extends ServiceProvider
{
    protected $defer = false;

    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        $configPath = __DIR__ . '/../config/communication-logger.php';

        $this->publishes([$configPath => config_path('communication-logger.php')], 'config');
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $configPath = __DIR__ . '/../config/communication-logger.php';
        $this->mergeConfigFrom($configPath, 'communication-logger');

        /** @var array $config */
        $config = $this->app['config']['communication-logger'];

        $this->registerEventFactory();
        $this->registerDatabaseAdapterFactory();
        $this->registerStoreAdapter($config);

        $this->app->bind(Logger::class, function () {
            return new Logger(
                $this->app[IAdapter::class],
                $this->app[IEventFactory::class]
            );
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['communication-logger'];
    }

    /**
     * Register database adapter factory.
     */
    public function registerDatabaseAdapterFactory()
    {
        $this->app->bind(ConnectionFactory::class, function () {
            return new ConnectionFactory();
        });
    }

    /**
     * Register event factory.
     */
    protected function registerEventFactory()
    {
        $this->app->bind(IEventFactory::class, function () {
            return new EventFactory();
        });
    }

    /**
     * Register store adapter.
     *
     * @param array $config
     */
    protected function registerStoreAdapter(array $config)
    {
        /** @var ConnectionFactory $factory */
        $factory = $this->app[ConnectionFactory::class];

        $this->app->bind(IAdapter::class, function () use ($factory, $config) {
            switch ($config['driver']) {
                case 'database':
                    $this->app->alias(IAdapter::class, DatabaseAdapter::class);

                    $connection = $factory->create(
                        $config['database']['type'],
                        $config['database']['host'],
                        $config['database']['port'],
                        $config['database']['name'],
                        $config['database']['username'],
                        $config['database']['password']
                    );

                    return new DatabaseAdapter($connection, $config['database']['table']);
                case 'memory':
                    $this->app->alias(IAdapter::class, MemoryAdapter::class);

                    return new MemoryAdapter();
                case 'file':
                default:
                    $this->app->alias(IAdapter::class, FileAdapter::class);

                    $this->app->bind(IWriter::class, function () {
                        return new Writer();
                    });

                    $this->app->bind(IReader::class, function () {
                        return new Reader();
                    });

                    if (!file_exists($config['file']['storage-dir'])) {
                        mkdir($config['file']['storage-dir'], 0777, true);
                    }

                    $logger = null;

                    try {
                        $logger = $this->app[MonoLog::class];
                    } catch (Exception $e) {
                    }

                    return new FileAdapter(
                        $this->app[IWriter::class],
                        $this->app[IReader::class],
                        $this->app[IEventFactory::class],
                        $config['file']['storage-dir'],
                        $logger
                    );
            }
        });
    }
}
