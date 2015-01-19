<?php

namespace DataDoggyDog\StatsdClient;

class SocketSender implements SenderInterface
{
    /** @var string */
    protected $hostname = 'localhost';
    /** @var int */
    protected $port = 8125;
    /** @var bool */
    protected $ignoreSocketErrors = true;
    /** @var resource|null */
    protected $socket = null;

    /**
     * @param string $hostname
     * @param int $port
     * @param bool $ignoreSocketErrors
     */
    public function __construct($hostname = 'localhost', $port = 8125, $ignoreSocketErrors = true)
    {
        $this->hostname = $hostname;
        $this->port = $port;
        $this->ignoreSocketErrors = (bool) $ignoreSocketErrors;
    }

    /**
     * @return bool
     */
    public function createSocket()
    {
        if ($this->socket) {
            throw new \RuntimeException('Already connected, close the connection before re-connecting');
        }
        $this->socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        socket_set_nonblock($this->socket);
        if (!$this->socket && !$this->ignoreSocketErrors) {
            throw new \RuntimeException('Cannot create UPD socket');
        }
        return ($this->socket !== null);
    }

    /**
     * Close the socket
     */
    public function closeSocket()
    {
        if ($this->socket) {
            socket_close($this->socket);
        }
        $this->socket = null;
    }

    /**
     * @param string $datagram
     * @param float $sampleRate
     *
     * @return int Number of packets sent to host
     */
    public function send($datagram, $sampleRate = 1.0)
    {
        if ($sampleRate < 1 && $sampleRate >= 0) {
            if (mt_rand() / mt_getrandmax() >= $sampleRate) {
                return 0;
            }
        }

        if (!$this->socket) {
            $socketCreated = $this->createSocket();
            if (!$socketCreated) {
                if ($this->ignoreSocketErrors) {
                    return 0;
                } else {
                    throw new \RuntimeException('No socket available to send to');
                }
            }
        }

        return socket_sendto($this->socket, $datagram, strlen($datagram), 0, $this->hostname, $this->port);
    }

}
