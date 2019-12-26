<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Component\Base64UID\Tests\Generator;

use GpsLab\Component\Base64UID\Generator\RandomCharGenerator;
use PHPUnit\Framework\TestCase;

class RandomCharGeneratorTest extends TestCase
{
    /**
     * @expectedException \GpsLab\Component\Base64UID\Exception\ArgumentTypeException
     */
    public function testUidLengthNoInteger()
    {
        $generator = new RandomCharGenerator('9');
    }

    /**
     * @expectedException \GpsLab\Component\Base64UID\Exception\InvalidArgumentException
     */
    public function testCharsetNoString()
    {
        $generator = new RandomCharGenerator(11, 11);
    }

    /**
     * @expectedException \GpsLab\Component\Base64UID\Exception\InvalidArgumentException
     */
    public function testNegativeUidLength()
    {
        $generator = new RandomCharGenerator(-1);
    }

    /**
     * @expectedException \GpsLab\Component\Base64UID\Exception\InvalidArgumentException
     */
    public function testZeroUidLength()
    {
        $generator = new RandomCharGenerator(0);
    }

    /**
     * @expectedException \GpsLab\Component\Base64UID\Exception\InvalidArgumentException
     */
    public function testInvalidTypeOfCharset()
    {
        $generator = new RandomCharGenerator(1, 1);
    }

    public function testGenerateDefault()
    {
        $generator = new RandomCharGenerator();
        $id = $generator->generate();
        $this->assertSame(10, strlen($id));
        $this->assertRegExp('/^[-_a-zA-Z0-9]{10}$/', $id);
    }

    public function testGenerateCustomLength()
    {
        $generator = new RandomCharGenerator(6);
        $id = $generator->generate();
        $this->assertSame(6, strlen($id));
        $this->assertRegExp('/^[-_a-zA-Z0-9]{6}$/', $id);
    }

    public function testGenerateCustomCharset()
    {
        $charset = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ+/';
        $generator = new RandomCharGenerator(11, $charset);
        $id = $generator->generate();
        $this->assertSame(11, strlen($id));
        $this->assertRegExp('/^[\/+a-zA-Z0-9]{11}$/', $id);
    }

    public function testGenerateCustomCharset2()
    {
        $charset = '0123456789abcdef';
        $generator = new RandomCharGenerator(11, $charset);
        $id = $generator->generate();
        $this->assertSame(11, strlen($id));
        $this->assertRegExp('/^[a-f0-9]{11}$/', $id);
    }
}
