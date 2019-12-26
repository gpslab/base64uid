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
use GpsLab\Component\Base64UID\Generator\RandomBinaryGenerator;
use PHPUnit\Framework\TestCase;

class RandomBinaryGeneratorTest extends TestCase
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
     * @expectedException \GpsLab\Component\Base64UID\Exception\InvalidArgumentException
     */
    public function testNegativeUidLength()
    {
        $generator = new RandomBinaryGenerator($this->encoder, -1);
    }

    /**
     * @expectedException \GpsLab\Component\Base64UID\Exception\InvalidArgumentException
     */
    public function testZeroUidLength()
    {
        $generator = new RandomBinaryGenerator($this->encoder, 0);
    }

    public function testGenerateDefault()
    {
        $generator = new RandomBinaryGenerator($this->encoder, PHP_INT_SIZE * 8);
        $id = $generator->generate();
        $this->assertRegExp('/^[\/+a-zA-Z0-9]{1,11}$/', $id);
    }

    public function testGenerateCustomLength()
    {
        $generator = new RandomBinaryGenerator($this->encoder, 6);
        $id = $generator->generate();
        $this->assertRegExp('/^[\/+a-zA-Z0-9]{1,11}$/', $id);
    }

    public function testGenerateCustomCharset()
    {
        $generator = new RandomBinaryGenerator($this->encoder, 11);
        $id = $generator->generate();
        $this->assertRegExp('/^[\/+a-zA-Z0-9]{1,11}$/', $id);
    }
}
