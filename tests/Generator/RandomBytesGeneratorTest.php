<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Component\Base64UID\Tests\Generator;

use GpsLab\Component\Base64UID\Generator\RandomBytesGenerator;
use PHPUnit\Framework\TestCase;

class RandomBytesGeneratorTest extends TestCase
{
    /**
     * @expectedException \GpsLab\Component\Base64UID\Exception\ArgumentTypeException
     */
    public function testBytesLengthNoInteger()
    {
        $generator = new RandomBytesGenerator('8');
    }

    /**
     * @expectedException \GpsLab\Component\Base64UID\Exception\InvalidArgumentException
     */
    public function testZeroBytesLength()
    {
        $generator = new RandomBytesGenerator(0);
    }

    public function testGenerateDefault()
    {
        $generator = new RandomBytesGenerator();
        $id = $generator->generate();
        $this->assertRegExp('/^[\/+a-zA-Z0-9]+$/', $id);
    }
}
