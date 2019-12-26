<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Component\Base64UID\Tests\Generator\Binary;

use GpsLab\Component\Base64UID\Generator\Binary\FloatingTimeGenerator;
use PHPUnit\Framework\TestCase;

class FloatingTimeGeneratorTest extends TestCase
{
    /**
     * @expectedException \GpsLab\Component\Base64UID\Exception\InvalidArgumentException
     */
    public function testTimeLengthNoInteger()
    {
        $generator = new FloatingTimeGenerator('45');
    }

    /**
     * @expectedException \GpsLab\Component\Base64UID\Exception\InvalidArgumentException
     */
    public function testLowTimeLength()
    {
        $generator = new FloatingTimeGenerator(0);
    }

    /**
     * @expectedException \GpsLab\Component\Base64UID\Exception\InvalidArgumentException
     */
    public function testBigTimeLength()
    {
        $generator = new FloatingTimeGenerator(64);
    }

    /**
     * @expectedException \GpsLab\Component\Base64UID\Exception\InvalidArgumentException
     */
    public function testLowTimeLengthForCurrentTime()
    {
        $min_time_length = strlen(decbin((int) floor(microtime(true) * 1000)));
        $generator = new FloatingTimeGenerator($min_time_length - 1);
    }

    /**
     * @return array
     */
    public function getTimeLengths()
    {
        return array(
            array(45),
            array(45),
            array(45),
            array(43),
            array(47),
        );
    }

    /**
     * @dataProvider getTimeLengths
     *
     * @param int $time_length
     */
    public function testGenerate($time_length)
    {
        $generator = new FloatingTimeGenerator($time_length);
        $id = $generator->generate();
        $this->assertInternalType('integer', $id);
    }
}
