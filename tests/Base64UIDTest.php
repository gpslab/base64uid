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
        $this->assertSame(10, strlen($id));
        $this->assertRegExp('/^[-_a-zA-Z0-9]{10}$/', $id);
    }

    public function testGenerateCustomLength()
    {
        $id = Base64UID::generate(6);
        $this->assertSame(6, strlen($id));
        $this->assertRegExp('/^[-_a-zA-Z0-9]{6}$/', $id);
    }

    public function testGenerateIncorrectLength()
    {
        $id = Base64UID::generate(-3);
        $this->assertEmpty($id);
    }

    public function testGenerateCustomCharset()
    {
        $charset = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ+/';
        $id = Base64UID::generate(11, $charset);
        $this->assertSame(11, strlen($id));
        $this->assertRegExp('/^[\/+a-zA-Z0-9]{11}$/', $id);
    }

    public function testGenerateCustomCharset2()
    {
        $charset = '0123456789abcdef';
        $id = Base64UID::generate(11, $charset);
        $this->assertSame(11, strlen($id));
        $this->assertRegExp('/^[a-f0-9]{11}$/', $id);
    }
}
