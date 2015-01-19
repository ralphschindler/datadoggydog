<?php

namespace DataDoggyDog\Test;

use DataDoggyDog\StatsdClient\DatagramSerializer;

class DatagramSerializerTest extends \PHPUnit_Framework_TestCase
{
    public function testSerializeMetricDatagram()
    {
        $serializer = new DatagramSerializer();
        $this->assertEquals(
            'foo:5|c|@1.000|#foo:bar',
            $serializer->serializeMetricDatagram('foo', 5, 'c', 1, ['foo' => 'bar'])
        );

        $this->assertEquals(
            'fooey:6|ms|@1.000|#foo,bar',
            $serializer->serializeMetricDatagram('fooey', 6, 'ms', 1, ['foo', 'bar'])
        );

        $this->assertEquals(
            'fooem:7|s|@1.000|#foo:bar,bam',
            $serializer->serializeMetricDatagram('fooem', 7, 's', 1, ['foo' => 'bar', 'bam'])
        );
    }

    public function testSerializeMetricDatagramThrowExceptionOnBadMetricName()
    {
        $serializer = new DatagramSerializer();

        $this->setExpectedException('InvalidArgumentException', 'The metric name must be a string with length greater than 0');
        $serializer->serializeMetricDatagram('', 2, 's');
    }

    public function testSerializeMetricDatagramThrowExceptionOnBadType()
    {
        $serializer = new DatagramSerializer();

        $this->setExpectedException('InvalidArgumentException', "Type must be one of 'c', 'g', 'h', 'ms', or 's'");
        $serializer->serializeMetricDatagram('foo', 2, 'x');
    }

    public function testSerializeMetricDatagramThrowExceptionOnBadSampleRate()
    {
        $serializer = new DatagramSerializer();

        $this->setExpectedException('InvalidArgumentException', 'Sample rate must be a floating point value between 0 and 1');
        $serializer->serializeMetricDatagram('foo', 2, 'c', 2);
    }
}
