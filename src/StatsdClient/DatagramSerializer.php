<?php

namespace DataDoggyDog\StatsdClient;

class DatagramSerializer
{
    public function serializeMetricDatagram($metricName, $value, $type, $sampleRate = 1.0, array $tags = array())
    {
        if ($sampleRate < 0 || $sampleRate > 1 || !is_numeric($sampleRate)) {
            throw new \InvalidArgumentException("Sample rate must be a floating point value between 0 and 1");
        }

        if (!in_array($type, ["c", "g", "h", "ms", "s"])) {
            throw new \InvalidArgumentException("Type must be one of 'c', 'g', 'h', 'ms', or 's'");
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

    public function serializeEventDatagram($title, $text, array $options = array())
    {
        $validOptions = [
            'd' => 'date_happened', // seconds (timestamp)
            'h' => 'hostname',
            'k' => 'aggregation_key', // string
            'p' => 'priority', // 'normal' | 'low' (default normal)
            's' => 'source_type_name',
            't' => 'alert_type', // Can be “error”, “warning”, “info” or “success”.
            '#' => 'tags'
        ];

        // @todo
    }
}
