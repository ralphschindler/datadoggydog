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

    public function send($metricDatagram, $sampleRate = 1.0)
    {
        $this->logger->info($metricDatagram);
    }
}
