<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Component\Base64UID\Tests\Generator\Binary;

use GpsLab\Component\Base64UID\Generator\Binary\RandomBinaryGenerator;
use PHPUnit\Framework\TestCase;

class RandomBinaryGeneratorTest extends TestCase
{
    /**
     * @expectedException \GpsLab\Component\Base64UID\Exception\ArgumentTypeException
     */
    public function testUidLengthNoInteger()
    {
        $generator = new RandomBinaryGenerator('9');
    }

    /**
     * @expectedException \GpsLab\Component\Base64UID\Exception\ZeroArgumentException
     */
    public function testNegativeUidLength()
    {
        $generator = new RandomBinaryGenerator(-1);
    }

    /**
     * @expectedException \GpsLab\Component\Base64UID\Exception\ZeroArgumentException
     */
    public function testZeroUidLength()
    {
        $generator = new RandomBinaryGenerator(0);
    }

    public function testGenerateDefault()
    {
        $generator = new RandomBinaryGenerator(PHP_INT_SIZE * 8);
        $id = $generator->generate();
        $this->assertInternalType('integer', $id);
    }

    public function testGenerateCustomLength()
    {
        $generator = new RandomBinaryGenerator(6);
        $id = $generator->generate();
        $this->assertInternalType('integer', $id);
    }

    public function testGenerateCustomCharset()
    {
        $generator = new RandomBinaryGenerator(11);
        $id = $generator->generate();
        $this->assertInternalType('integer', $id);
    }

    public function testNotSame()
    {
        $generator = new RandomBinaryGenerator(11);
        self::assertNotSame($generator->generate(), $generator->generate());
    }
}
