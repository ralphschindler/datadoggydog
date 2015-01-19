<?php

namespace DataDoggyDog\Test;

use DataDoggyDog\StatsdClient\StatsdClient;

class StatsdClientTest extends \PHPUnit_Framework_TestCase
{
    /** @var StatsdClient */
    protected $statsdClient;
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $mockSender;
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $mockDatagramSerializer;

    public function setup()
    {
        $this->mockSender = $this->getMock('DataDoggyDog\StatsdClient\SocketSender', ['send']);
        $this->mockDatagramSerializer = $this->getMock('DataDoggyDog\StatsdClient\DatagramSerializer', ['serializeMetricDatagram']);
        $this->statsdClient = new StatsdClient($this->mockSender, $this->mockDatagramSerializer);
    }

    public function testIncrement()
    {
        $this->mockDatagramSerializer
            ->expects($this->once())
            ->method('serializeMetricDatagram')
            ->with('foo.bar', 1, 'c', 1.0)
            ->willReturn('foo.bar');
        $this->mockSender->expects($this->once())->method('send')->with($this->equalTo('foo.bar'), $this->equalTo(1.0));
        $this->statsdClient->increment('foo.bar', 1);
    }

    public function testDecrement()
    {
        $this->mockDatagramSerializer
            ->expects($this->once())
            ->method('serializeMetricDatagram')
            ->with('foo.bar', -1, 'c', 1.0)
            ->willReturn('foo.bar');
        $this->mockSender->expects($this->once())->method('send')->with($this->equalTo('foo.bar'), $this->equalTo(1.0));
        $this->statsdClient->decrement('foo.bar', 1);
    }

    public function testGuage()
    {
        $this->mockDatagramSerializer
            ->expects($this->once())
            ->method('serializeMetricDatagram')
            ->with('foo.bar', 20, 'g', 1.0)
            ->willReturn('foo.bar');
        $this->mockSender->expects($this->once())->method('send')->with($this->equalTo('foo.bar'), $this->equalTo(1.0));
        $this->statsdClient->gauge('foo.bar', 20);
    }

    public function testHistogram()
    {
        $this->mockDatagramSerializer
            ->expects($this->once())
            ->method('serializeMetricDatagram')
            ->with('foo.bar', 30, 'h', 1.0)
            ->willReturn('foo.bar');
        $this->mockSender->expects($this->once())->method('send')->with($this->equalTo('foo.bar'), $this->equalTo(1.0));
        $this->statsdClient->histogram('foo.bar', 30);
    }

    public function testTiming()
    {
        $this->mockDatagramSerializer
            ->expects($this->once())
            ->method('serializeMetricDatagram')
            ->with('foo.bar', 40, 'ms', 1.0)
            ->willReturn('foo.bar');
        $this->mockSender->expects($this->once())->method('send')->with($this->equalTo('foo.bar'), $this->equalTo(1.0));
        $this->statsdClient->timing('foo.bar', 40);
    }

    public function testSet()
    {
        $this->mockDatagramSerializer
            ->expects($this->once())
            ->method('serializeMetricDatagram')
            ->with('foo.bar', 40, 's', 1.0)
            ->willReturn('foo.bar');
        $this->mockSender->expects($this->once())->method('send')->with($this->equalTo('foo.bar'), $this->equalTo(1.0));
        $this->statsdClient->set('foo.bar', 40);
    }

}
