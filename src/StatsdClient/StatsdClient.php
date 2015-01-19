<?php

namespace DataDoggyDog\StatsdClient;

class StatsdClient
{
    /** @var SenderInterface */
    private $sender;

    /** @var DatagramSerializer */
    private $datagramSerializer;

    /** @var string */
    protected $metricNamespace = '';

    /**
     * @param SenderInterface $sender
     * @param DatagramSerializer $datagramSerializer
     */
    public function __construct(SenderInterface $sender = null, DatagramSerializer $datagramSerializer = null)
    {
        $this->sender = ($sender) ?: new SocketSender();
        $this->datagramSerializer = ($datagramSerializer) ?: new DatagramSerializer();
    }

    /**
     * @return SocketSender
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * @return DatagramSerializer
     */
    public function getDatagramSerializer()
    {
        return $this->datagramSerializer;
    }

    /**
     * If namespace does not include a dot separator, one will be added
     * @param $namespace
     */
    public function setMetricNamespace($namespace)
    {
        if ($namespace != '') {
            $namespace = rtrim($namespace, '.') . '.';
        }
        $this->metricNamespace = $namespace;
    }

    public function getMetricNamespace()
    {
        return $this->metricNamespace;
    }

    /**
     * @param string $name
     * @param int    $value
     * @param array  $tags
     * @param float  $sampleRate
     *
     * @return bool
     */
    public function increment($name, $value = 1, array $tags = [], $sampleRate = 1.0)
    {
        $name = $this->metricNamespace . $name;
        return $this->sender->send(
            $this->datagramSerializer->serializeMetricDatagram($name, $value, 'c', $sampleRate, $tags)
        );
    }

    /**
     * @param string $name
     * @param int    $value
     * @param array  $tags
     * @param float  $sampleRate
     *
     * @return bool
     */
    public function decrement($name, $value = 1, array $tags = [], $sampleRate = 1.0)
    {
        $name = $this->metricNamespace . $name;
        return $this->sender->send(
            $this->datagramSerializer->serializeMetricDatagram($name, (int) -$value, 'c', $sampleRate, $tags)
        );
    }

    /**
     * @param string $name
     * @param mixed  $value
     * @param array  $tags
     * @param float  $sampleRate
     *
     * @return bool
     */
    public function gauge($name, $value, array $tags = [], $sampleRate = 1.0)
    {
        $name = $this->metricNamespace . $name;
        return $this->sender->send(
            $this->datagramSerializer->serializeMetricDatagram($name, $value, 'g', $sampleRate, $tags)
        );
    }

    /**
     * @param string $name
     * @param mixed  $value
     * @param array  $tags
     * @param float  $sampleRate
     *
     * @return bool
     */
    public function histogram($name, $value, array $tags = [], $sampleRate = 1.0)
    {
        $name = $this->metricNamespace . $name;
        $this->sender->send(
            $this->datagramSerializer->serializeMetricDatagram($name, $value, 'h', $sampleRate, $tags)
        );
    }

    /**
     * @param string $name
     * @param mixed  $value
     * @param array  $tags
     * @param float  $sampleRate
     *
     * @return bool
     */
    public function timing($name, $value, array $tags = [], $sampleRate = 1.0)
    {
        $name = $this->metricNamespace . $name;
        $this->sender->send(
            $this->datagramSerializer->serializeMetricDatagram($name, $value, 'ms', $sampleRate, $tags)
        );
    }

    /**
     * @param string $name
     * @param mixed  $value
     * @param array  $tags
     * @param float  $sampleRate
     *
     * @return bool
     */
    public function set($name, $value, array $tags = [], $sampleRate = 1.0)
    {
        $name = $this->metricNamespace . $name;
        $this->sender->send(
            $this->datagramSerializer->serializeMetricDatagram($name, $value, 's', $sampleRate, $tags)
        );
    }

}
