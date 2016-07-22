<?php

namespace spec\RawPHP\CommunicationLogger;

use Prophecy\Argument;
use RawPHP\CommunicationLogger\Adapter\IAdapter;
use RawPHP\CommunicationLogger\Factory\IEventFactory;
use RawPHP\CommunicationLogger\Logger;
use PhpSpec\ObjectBehavior;
use RawPHP\CommunicationLogger\Model\IEvent;

class LoggerSpec extends ObjectBehavior
{
    function let(IAdapter $adapter, IEventFactory $eventFactory)
    {
        $this->beConstructedWith($adapter, $eventFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Logger::class);
    }

    function it_handles_the_begining_of_a_request(IAdapter $adapter, IEventFactory $eventFactory, IEvent $event)
    {
        $eventFactory->create('request', 'http://example.com', 'POST', 'ref')
                     ->shouldBeCalled()
                     ->willReturn($event);

        $adapter->save($event)->shouldBeCalled()->willReturn($event);

        $this->begin('request', 'http://example.com', 'POST', 'ref')
             ->shouldReturn($event);
    }

    function it_handles_the_end_of_a_request(IAdapter $adapter, IEvent $event)
    {
        $adapter->getLastEvent()->willReturn($event);

        $event->setResponse('response')->shouldBeCalled();
        $event->setLatency(Argument::type('double'))->shouldBeCalled();

        $adapter->save($event)->shouldBeCalled();

        $this->end('response');
    }
}
