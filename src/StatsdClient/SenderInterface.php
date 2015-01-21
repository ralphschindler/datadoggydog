<?php

namespace DataDoggyDog\StatsdClient;

interface SenderInterface
{
    public function send($datagram, $sampleRate = 1.0);
}
