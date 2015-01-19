<?php

namespace DataDoggyDog\StatsdClient;

use Psr\Log\LoggerInterface;

class LogSender implements SenderInterface
{
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function send($datagram, $sampleRate)
    {
        $this->logger->info($datagram);
    }
}
