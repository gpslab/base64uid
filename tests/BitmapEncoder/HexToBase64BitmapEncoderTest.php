<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Component\Base64UID\Tests\BitmapEncoder;

use GpsLab\Component\Base64UID\BitmapEncoder\HexToBase64BitmapEncoder;
use PHPUnit\Framework\TestCase;

class HexToBase64BitmapEncoderTest extends TestCase
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
     * @return array
     */
    public function getBitmaps()
    {
        return array(
            array(PHP_INT_MIN, 'gAAAAAAAAAA'),
            array(PHP_INT_MAX, 'f/////////8'),
            array(1234567890, 'SZYC0g'),
            array(010110101101110101101, 'IJBBIJIIJBA'),
        );
    }

    /**
     * @dataProvider getBitmaps
     *
     * @param int    $bitmap
     * @param string $encoded
     */
    public function testEncoder($bitmap, $encoded)
    {
        $this->assertSame($encoded, $this->encoder->encoder($bitmap));
    }
}
