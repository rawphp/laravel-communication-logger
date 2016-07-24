<?php

namespace spec\RawPHP\LaravelCommunicationLogger\Middleware;

use GuzzleHttp\Psr7\Request as GuzzleRequest;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Prophecy\Argument;
use RawPHP\CommunicationLogger\CommunicationLogger;
use RawPHP\CommunicationLogger\Model\IEvent;
use RawPHP\CommunicationLogger\Util\CommunicationExtractor;
use RawPHP\LaravelCommunicationLogger\Middleware\CommunicationLog;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\HeaderBag;

class CommunicationLogSpec extends ObjectBehavior
{
    function let(CommunicationLogger $logger, CommunicationExtractor $extractor)
    {
        $this->beConstructedWith($logger, $extractor);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CommunicationLog::class);
    }

    function it_handles_communication_logging(
        CommunicationLogger $logger,
        CommunicationExtractor $extractor,
        Request $request,
        Response $response,
        HeaderBag $requestHeaders,
        IEvent $event
    ) {
        $request->getMethod()->shouldBeCalled()->willReturn('POST');
        $request->getContent()->shouldBeCalled()->willReturn('request');
        $request->getUri()->shouldBeCalled()->willReturn('http://example.com');
        $request->headers = $requestHeaders;

        $requestHeaders->all()->shouldBeCalled()->willReturn([]);

        $next = function (Request $request) use ($response) {
            return new Response();
        };

        $extractor->getRequest(Argument::type(GuzzleRequest::class))->shouldBeCalled()->willReturn('request');
        $extractor->getResponse(Argument::type(GuzzleResponse::class))->shouldBeCalled()->willReturn('response');

        $logger->begin('request', 'http://example.com', 'POST', '')->shouldBeCalled()->willReturn($event);
        $logger->end($event, 'response')->shouldBeCalled();

        $this->handle($request, $next);
    }
}
