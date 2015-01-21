<?php

namespace DataDoggyDog\Test;

use DataDoggyDog\StatsdClient\StatsdClient;

class StatsdClientTest extends \PHPUnit_Framework_TestCase
{
    /** @var StatsdClient */
    protected $statsdClient;
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $mockSender;

    public function setup()
    {
        $this->mockSender = $this->getMock('DataDoggyDog\StatsdClient\SocketSender', ['send']);
        $this->statsdClient = new StatsdClient($this->mockSender);
    }

    public function testIncrement()
    {
        $this->mockSender->expects($this->once())->method('send')->with($this->equalTo('foo.bar:1|c|@1.000'), $this->equalTo(1.0));
        $this->statsdClient->increment('foo.bar', 1);
    }

    public function testDecrement()
    {
        $this->mockSender->expects($this->once())->method('send')->with($this->equalTo('foo.bar:-1|c|@1.000'), $this->equalTo(1.0));
        $this->statsdClient->decrement('foo.bar', 1);
    }

    public function testGauge()
    {
        $this->mockSender->expects($this->once())->method('send')->with($this->equalTo('foo.bar:20|g|@1.000'), $this->equalTo(1.0));
        $this->statsdClient->gauge('foo.bar', 20);
    }

    public function testHistogram()
    {
        $this->mockSender->expects($this->once())->method('send')->with($this->equalTo('foo.bar:30|h|@1.000'), $this->equalTo(1.0));
        $this->statsdClient->histogram('foo.bar', 30);
    }

    public function testTiming()
    {
        $this->mockSender->expects($this->once())->method('send')->with($this->equalTo('foo.bar:40|ms|@1.000'), $this->equalTo(1.0));
        $this->statsdClient->timing('foo.bar', 40);
    }

    public function testSet()
    {
        $this->mockSender->expects($this->once())->method('send')->with($this->equalTo('foo.bar:40|s|@1.000'), $this->equalTo(1.0));
        $this->statsdClient->set('foo.bar', 40);
    }

}
