<?php

namespace DataDoggyDog\StatsdClient;

class StatsdClient
{
    /** @var SenderInterface */
    private $sender;

    /** @var string */
    protected $metricNamespace = '';

    /**
     * @param SenderInterface $sender
     */
    public function __construct(SenderInterface $sender = null)
    {
        $this->sender = ($sender) ?: new SocketSender();
    }

    /**
     * @return SocketSender
     */
    public function getSender()
    {
        return $this->sender;
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
     * @param int $value
     * @param array $tags
     * @param float $sampleRate
     *
     * @return bool
     */
    public function increment($name, $value = 1, array $tags = [], $sampleRate = 1.0)
    {
        $name = $this->metricNamespace . $name;
        return $this->sender->send(
            $this->serializeMetricDatagram($name, $value, 'c', $sampleRate, $tags),
            $sampleRate
        );
    }

    /**
     * @param string $name
     * @param int $value
     * @param array $tags
     * @param float $sampleRate
     *
     * @return bool
     */
    public function decrement($name, $value = 1, array $tags = [], $sampleRate = 1.0)
    {
        $name = $this->metricNamespace . $name;
        return $this->sender->send(
            $this->serializeMetricDatagram($name, (int)-$value, 'c', $sampleRate, $tags),
            $sampleRate
        );
    }

    /**
     * @param string $name
     * @param mixed $value
     * @param array $tags
     * @param float $sampleRate
     *
     * @return bool
     */
    public function gauge($name, $value, array $tags = [], $sampleRate = 1.0)
    {
        $name = $this->metricNamespace . $name;
        return $this->sender->send(
            $this->serializeMetricDatagram($name, $value, 'g', $sampleRate, $tags),
            $sampleRate
        );
    }

    /**
     * @param string $name
     * @param mixed $value
     * @param array $tags
     * @param float $sampleRate
     *
     * @return bool
     */
    public function histogram($name, $value, array $tags = [], $sampleRate = 1.0)
    {
        $name = $this->metricNamespace . $name;
        $this->sender->send(
            $this->serializeMetricDatagram($name, $value, 'h', $sampleRate, $tags),
            $sampleRate
        );
    }

    /**
     * @param string $name
     * @param mixed $value
     * @param array $tags
     * @param float $sampleRate
     *
     * @return bool
     */
    public function timing($name, $value, array $tags = [], $sampleRate = 1.0)
    {
        $name = $this->metricNamespace . $name;
        $this->sender->send(
            $this->serializeMetricDatagram($name, $value, 'ms', $sampleRate, $tags),
            $sampleRate
        );
    }

    /**
     * @param string $name
     * @param mixed $value
     * @param array $tags
     * @param float $sampleRate
     *
     * @return bool
     */
    public function set($name, $value, array $tags = [], $sampleRate = 1.0)
    {
        $name = $this->metricNamespace . $name;
        $this->sender->send(
            $this->serializeMetricDatagram($name, $value, 's', $sampleRate, $tags),
            $sampleRate
        );
    }

    public function event($title, $text, array $optionalMetadatas = array())
    {
        static $validOptionalMetadatas = [
            'd' => 'date_happened', // seconds (timestamp)
            'h' => 'hostname',
            'k' => 'aggregation_key', // string
            'p' => 'priority', // 'normal' | 'low' (default normal)
            's' => 'source_type_name',
            't' => 'alert_type', // Can be “error”, “warning”, “info” or “success”.
            '#' => 'tags'
        ];
        static $validLongOptionalMetadatas = array();
        if (!$validLongOptionalMetadatas) {
            $validLongOptionalMetadatas = array_flip($validOptionalMetadatas);
        }

        $datagram = '_e{' . strlen($title) . ',' . strlen($text) . '}:' . $title . '|' . $text;

        foreach ($optionalMetadatas as $optionalMetadataKey => $optionalMetadata) {
            if (!isset($validOptionalMetadatas[$optionalMetadataKey]) && !isset($validLongOptionalMetadatas[$optionalMetadataKey])) {
                throw new \InvalidArgumentException("They key name provided $optionalMetadataKey is not supported");
            }
            $key = (strlen($optionalMetadataKey) == 1) ? $optionalMetadataKey : $validOptionalMetadatas[$optionalMetadataKey];
            switch ($key) {
                // @todo add key specific validation
                default:
            }
            $datagram .= "$key:$optionalMetadata";
        }

        $this->sender->send($datagram);
    }

    protected function serializeMetricDatagram($metricName, $value, $type, $sampleRate = 1.0, array $tags = array())
    {
        if (strlen($metricName) == 0) {
            throw new \InvalidArgumentException('The metric name must be a string with length greater than 0');
        }

        if ($sampleRate < 0 || $sampleRate > 1 || !is_numeric($sampleRate)) {
            throw new \InvalidArgumentException("Sample rate must be a floating point value between 0 and 1");
        }

        $datagram = '';

        $datagram .= sprintf('%s:%s|%s|@%0.3f', $metricName, $value, $type, (float) $sampleRate);

        // handle tags
        if (count($tags)) {
            $datagram .= '|#';
            $datagramTags = [];
            foreach ($tags as $i => $tag) {
                if (is_array($tag)) {
                    $datagramTags[] = array_keys($tag)[0] . ":" . array_values($tag)[0];
                } elseif (is_string($i) && !is_numeric($i)) {
                    $datagramTags[] = $i . ':' . $tag;
                } else {
                    $datagramTags[] = $tag;
                }
            }
            $datagram .= implode(",", $datagramTags);
        }

        return $datagram;
    }

}
