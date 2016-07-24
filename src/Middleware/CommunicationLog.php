<?php

namespace RawPHP\LaravelCommunicationLogger\Middleware;

use Closure;
use Exception;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;
use Illuminate\Http\Request as LaravelRequest;
use Illuminate\Http\Response as LaravelResponse;
use RawPHP\CommunicationLogger\CommunicationLogger;
use RawPHP\CommunicationLogger\Model\IEvent;
use RawPHP\CommunicationLogger\Util\CommunicationExtractor;

/**
 * Class CommunicationLogger
 *
 * @package RawPHP\LaravelCommunicationLogger\Middleware
 */
class CommunicationLog
{
    /** @var  CommunicationLogger */
    protected $logger;
    /** @var  CommunicationExtractor */
    protected $extractor;

    /**
     * CommunicationsLogger constructor.
     *
     * @param CommunicationLogger $logger
     * @param CommunicationExtractor $extractor
     */
    public function __construct(CommunicationLogger $logger, CommunicationExtractor $extractor)
    {
        $this->logger = $logger;
        $this->extractor = $extractor;
    }

    /**
     * Handle an incoming request.
     *
     * @param  LaravelRequest $request
     * @param  Closure $next
     *
     * @return mixed
     */
    public function handle(LaravelRequest $request, Closure $next)
    {
        $message = (new Request(
            $request->getMethod(),
            new Uri($request->getUri()),
            $request->headers->all(),
            $request->getContent()
        ));

        $result = $this->extractor->getRequest($message);

        $event = $this->logger->begin(
            $result['request'],
            $request->getUri(),
            $request->getMethod(),
            ''
        );

        $response = null;

        try {
            /** @var LaravelResponse $response */
            $response = $next($request);
        } catch (Exception $e) {
            $response = LaravelResponse::create($e->getMessage(), 500);
        } finally {
            $this->logResponse($response, $event);
        }

        return $response;
    }

    /**
     * @param LaravelResponse $response
     * @param IEvent $event
     */
    protected function logResponse(LaravelResponse $response, IEvent $event)
    {
        $message = (new Response(
            $response->getStatusCode(),
            $response->headers->all(),
            $response->getContent()
        ));

        $result = $this->extractor->getResponse($message);

        $this->logger->end($event, $result['response']);
    }
}
