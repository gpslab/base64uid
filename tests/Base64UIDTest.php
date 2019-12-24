<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Component\Base64UID\Tests;

use GpsLab\Component\Base64UID\Base64UID;
use PHPUnit\Framework\TestCase;

class Base64UIDTest extends TestCase
{
    public function testGenerateDefault()
    {
        $id = Base64UID::generate();
        $this->assertEquals(10, strlen($id));
    }

    public function testGenerateCustomLength()
    {
        $id = Base64UID::generate(6);
        $this->assertEquals(6, strlen($id));
    }

    public function testGenerateIncorrectLength()
    {
        $id = Base64UID::generate(-3);
        $this->assertEquals('', $id);
    }

    public function testGenerateCustomCharset()
    {
        $charset = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ+/';
        $id = Base64UID::generate(11, $charset);
        $this->assertEquals(11, strlen($id));
    }

    public function testGenerateCustomCharset2()
    {
        $charset = '0123456789abcdef';
        $id = Base64UID::generate(11, $charset);
        $this->assertEquals(11, strlen($id));
    }
}
