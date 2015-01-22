<?php

namespace DataDoggyDog\Test;

use DataDoggyDog\StatsdClient\SocketSender;

class SocketSenderTest extends \PHPUnit_Framework_TestCase
{
    protected $testSocket;

    public function setup()
    {
        $this->testSocket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
    }

    public function teardown()
    {

    }

    public function testSocketSend()
    {
        socket_bind($this->testSocket, '127.0.0.1', 8125);

        $socketSender = new SocketSender('127.0.0.1');
        $socketSender->send('hello!');

        $input = socket_read($this->testSocket, 1024);
        $this->assertEquals('hello!', $input);
    }

}
