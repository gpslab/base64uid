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

class Base64UIDTest extends \PHPUnit_Framework_TestCase
{
    public function testGenerate()
    {
        $this->assertEquals(10, strlen(Base64UID::generate()));
        $this->assertEquals(6, strlen(Base64UID::generate(6)));
        $this->assertEquals(0, strlen(Base64UID::generate(-3)));
    }
}
