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
     * @expectedException \GpsLab\Component\Base64UID\Exception\SmallBitModeException
     */
    public function testProcessorArchitectureException()
    {
        if (PHP_INT_SIZE * 8 >= 64) {
            $this->markTestSkipped('This test is not reproducible on this processor architecture.');
        }

        $generator = new FloatingTimeGenerator();
    }

    /**
     * @expectedException \GpsLab\Component\Base64UID\Exception\ArgumentTypeException
     */
    public function testTimeLengthNoInteger()
    {
        $generator = new FloatingTimeGenerator('45');
    }

    /**
     * @expectedException \GpsLab\Component\Base64UID\Exception\ArgumentTypeException
     */
    public function testTimeOffsetNoInteger()
    {
        $generator = new FloatingTimeGenerator(45, '123');
    }

    /**
     * @expectedException \GpsLab\Component\Base64UID\Exception\ZeroArgumentException
     */
    public function testZeroTimeLength()
    {
        $generator = new FloatingTimeGenerator(-1);
    }

    /**
     * @expectedException \GpsLab\Component\Base64UID\Exception\ZeroArgumentException
     */
    public function testZeroTimeOffset()
    {
        $generator = new FloatingTimeGenerator(45, -1);
    }

    /**
     * @expectedException \GpsLab\Component\Base64UID\Exception\ArgumentRangeException
     */
    public function testLowTimeLength()
    {
        $generator = new FloatingTimeGenerator(0);
    }

    /**
     * @expectedException \GpsLab\Component\Base64UID\Exception\ArgumentRangeException
     */
    public function testBigTimeLength()
    {
        $generator = new FloatingTimeGenerator(64);
    }

    /**
     * @expectedException \GpsLab\Component\Base64UID\Exception\ArgumentRangeException
     */
    public function testLowTimeLengthForCurrentTime()
    {
        $min_time_length = strlen(decbin((int) floor(microtime(true) * 1000)));
        $generator = new FloatingTimeGenerator($min_time_length - 1);
    }

    /**
     * @expectedException \GpsLab\Component\Base64UID\Exception\ArgumentRangeException
     */
    public function testGrateTimeOffsetForCurrentTime()
    {
        $now = (int) floor(microtime(true) * 1000);
        $generator = new FloatingTimeGenerator(45, $now + 100);
    }

    /**
     * @expectedException \GpsLab\Component\Base64UID\Exception\ArgumentRangeException
     */
    public function testLowTimeLengthForCurrentTimeWithTimeOffset()
    {
        $now = (int) floor(microtime(true) * 1000);
        $offset = strtotime('2000-01-01 00:00:00') * 1000;
        $min_time_length = strlen(decbin($offset).decbin($now));

        $generator = new FloatingTimeGenerator($min_time_length, $offset);
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

    public function testNotSame()
    {
        $generator = new FloatingTimeGenerator();
        self::assertNotSame($generator->generate(), $generator->generate());
    }
}
