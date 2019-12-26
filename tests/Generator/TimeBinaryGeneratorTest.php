<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Component\Base64UID\Tests\Generator;

use GpsLab\Component\Base64UID\BitmapEncoder\HexToBase64BitmapEncoder;
use GpsLab\Component\Base64UID\Generator\TimeBinaryGenerator;
use PHPUnit\Framework\TestCase;

class TimeBinaryGeneratorTest extends TestCase
{
    /**
     * @var HexToBase64BitmapEncoder
     */
    private $encoder;

    protected function setUp()
    {
        $this->encoder = new HexToBase64BitmapEncoder();
    }

    /**
     * @expectedException \GpsLab\Component\Base64UID\Exception\SmallBitModeException
     */
    public function testSmallBitMode()
    {
        if (PHP_INT_SIZE * 8 >= 64) {
            $this->markTestSkipped('This test is not reproducible on this architecture.');
        }

        $generator = new TimeBinaryGenerator($this->encoder, 9, 45);
    }

    /**
     * @expectedException \GpsLab\Component\Base64UID\Exception\InvalidArgumentException
     */
    public function testPrefixLengthNoInteger()
    {
        $generator = new TimeBinaryGenerator($this->encoder, '9', 45);
    }

    /**
     * @expectedException \GpsLab\Component\Base64UID\Exception\InvalidArgumentException
     */
    public function testTimeLengthNoInteger()
    {
        $generator = new TimeBinaryGenerator($this->encoder, 9, '45');
    }

    /**
     * @expectedException \GpsLab\Component\Base64UID\Exception\InvalidArgumentException
     */
    public function testLowPrefixLength()
    {
        $generator = new TimeBinaryGenerator($this->encoder, -1, 45);
    }

    /**
     * @expectedException \GpsLab\Component\Base64UID\Exception\InvalidArgumentException
     */
    public function testLowTimeLength()
    {
        $generator = new TimeBinaryGenerator($this->encoder, 9, 0);
    }

    /**
     * @expectedException \GpsLab\Component\Base64UID\Exception\InvalidArgumentException
     */
    public function testBigPrefixLength()
    {
        $generator = new TimeBinaryGenerator($this->encoder, 19, 45);
    }

    /**
     * @expectedException \GpsLab\Component\Base64UID\Exception\InvalidArgumentException
     */
    public function testLowTimeLengthForCurrentTime()
    {
        $min_time_length = strlen(decbin((int) floor(microtime(true) * 1000)));
        $generator = new TimeBinaryGenerator($this->encoder, 0, $min_time_length - 1);
    }

    /**
     * @return array
     */
    public function getTimeAndPrefixLengths()
    {
        return array(
            array(0, 45),
            array(9, 45),
            array(18, 45),
            array(20, 43),
            array(16, 47),
        );
    }

    /**
     * @dataProvider getTimeAndPrefixLengths
     *
     * @param int $prefix_length
     * @param int $time_length
     */
    public function testGenerate($prefix_length, $time_length)
    {
        $generator = new TimeBinaryGenerator($this->encoder, $prefix_length, $time_length);
        $id = $generator->generate();
        $this->assertSame(11, strlen($id));
        $this->assertRegExp('/^[\/+a-zA-Z0-9]{11}$/', $id);
    }
}
