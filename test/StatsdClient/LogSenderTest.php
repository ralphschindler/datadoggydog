<?php

namespace DataDoggyDog\Test;

use DataDoggyDog\StatsdClient\LogSender;
use Psr\Log\LoggerInterface;

class LogSenderTest extends \PHPUnit_Framework_TestCase
{
    public function testSend()
    {
        $logger = $this->getMockForAbstractClass(LoggerInterface::class, ['info']);
        $logger->expects($this->once())->method('info')->with('Hello');
        $logSender = new LogSender($logger);
        $logSender->send('Hello');
    }
}
