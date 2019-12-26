<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Component\Base64UID\Tests\Generator\Binary;

use GpsLab\Component\Base64UID\Generator\Binary\SnowflakeGenerator;
use PHPUnit\Framework\TestCase;

class SnowflakeGeneratorTest extends TestCase
{
    /**
     * @expectedException \GpsLab\Component\Base64UID\Exception\ArgumentTypeException
     */
    public function testDataCenterNoInteger()
    {
        $generator = new SnowflakeGenerator('1', 1);
    }

    /**
     * @expectedException \GpsLab\Component\Base64UID\Exception\ArgumentTypeException
     */
    public function testMachineNoInteger()
    {
        $generator = new SnowflakeGenerator(1, '1');
    }

    /**
     * @expectedException \GpsLab\Component\Base64UID\Exception\ArgumentTypeException
     */
    public function testTimeOffsetNoInteger()
    {
        $generator = new SnowflakeGenerator(1, 1, '0');
    }

    /**
     * @expectedException \GpsLab\Component\Base64UID\Exception\ZeroArgumentException
     */
    public function testNegativeDataCenter()
    {
        $generator = new SnowflakeGenerator(-1, 1);
    }

    /**
     * @expectedException \GpsLab\Component\Base64UID\Exception\ZeroArgumentException
     */
    public function testNegativeMachine()
    {
        $generator = new SnowflakeGenerator(1, -1);
    }

    /**
     * @expectedException \GpsLab\Component\Base64UID\Exception\ZeroArgumentException
     */
    public function testNegativeTimeOffset()
    {
        $generator = new SnowflakeGenerator(1, 1, -1);
    }

    /**
     * @expectedException \GpsLab\Component\Base64UID\Exception\ArgumentRangeException
     */
    public function testRangeDataCenter()
    {
        $generator = new SnowflakeGenerator(32, 1);
    }

    /**
     * @expectedException \GpsLab\Component\Base64UID\Exception\ArgumentRangeException
     */
    public function testRangeMachine()
    {
        $generator = new SnowflakeGenerator(1, 128);
    }

    /**
     * @expectedException \GpsLab\Component\Base64UID\Exception\ArgumentRangeException
     */
    public function testGrateTimeOffsetForCurrentTime()
    {
        $now = (int) floor(microtime(true) * 1000);
        $generator = new SnowflakeGenerator(1, 1, $now + 100);
    }

    /**
     * @return array
     */
    public function getGenerateParams()
    {
        return array(
            array(1, 1, 0),
            array(2, 2, 123),
            array(31, 127, (int) floor(microtime(true) * 1000) - 1),
        );
    }

    /**
     * @dataProvider getGenerateParams
     *
     * @param int $data_center
     * @param int $machine
     * @param int $time_offset
     */
    public function testGenerate($data_center, $machine, $time_offset)
    {
        $generator = new SnowflakeGenerator($data_center, $machine, $time_offset);
        $id = $generator->generate();
        $this->assertInternalType('integer', $id);
    }
}
