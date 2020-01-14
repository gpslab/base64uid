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
     * @expectedException \GpsLab\Component\Base64UID\Exception\ProcessorArchitectureException
     */
    public function testProcessorArchitectureException()
    {
        if (PHP_INT_SIZE * 8 >= 64) {
            $this->markTestSkipped('This test is not reproducible on this processor architecture.');
        }

        $generator = new SnowflakeGenerator(1);
    }

    /**
     * @expectedException \GpsLab\Component\Base64UID\Exception\ArgumentTypeException
     */
    public function testGeneratorNoInteger()
    {
        $generator = new SnowflakeGenerator('1');
    }

    /**
     * @expectedException \GpsLab\Component\Base64UID\Exception\ArgumentTypeException
     */
    public function testGeneratorLengthNoInteger()
    {
        $generator = new SnowflakeGenerator(1, '1');
    }

    /**
     * @expectedException \GpsLab\Component\Base64UID\Exception\ArgumentTypeException
     */
    public function testSequenceLengthNoInteger()
    {
        $generator = new SnowflakeGenerator(1, 1, '1');
    }

    /**
     * @expectedException \GpsLab\Component\Base64UID\Exception\ArgumentTypeException
     */
    public function testTimeLengthNoInteger()
    {
        $generator = new SnowflakeGenerator(1, 1, 1, '1');
    }

    /**
     * @expectedException \GpsLab\Component\Base64UID\Exception\ArgumentTypeException
     */
    public function testTimeOffsetNoInteger()
    {
        $generator = new SnowflakeGenerator(1, 1, 1, 1, '1');
    }

    /**
     * @expectedException \GpsLab\Component\Base64UID\Exception\ZeroArgumentException
     */
    public function testNegativeGenerator()
    {
        $generator = new SnowflakeGenerator(-1);
    }

    /**
     * @expectedException \GpsLab\Component\Base64UID\Exception\ZeroArgumentException
     */
    public function testNegativeGeneratorLength()
    {
        $generator = new SnowflakeGenerator(1, -1);
    }

    /**
     * @expectedException \GpsLab\Component\Base64UID\Exception\ZeroArgumentException
     */
    public function testNegativeSequenceLength()
    {
        $generator = new SnowflakeGenerator(1, 1, -1);
    }

    /**
     * @expectedException \GpsLab\Component\Base64UID\Exception\ZeroArgumentException
     */
    public function testNegativeTimeLength()
    {
        $generator = new SnowflakeGenerator(1, 1, 1, -1);
    }

    /**
     * @expectedException \GpsLab\Component\Base64UID\Exception\ZeroArgumentException
     */
    public function testNegativeTimeOffset()
    {
        $generator = new SnowflakeGenerator(1, 1, 1, 1, -1);
    }

    /**
     * @return array
     */
    public function getOverflowIntRanges()
    {
        return array(
            array(62, 1, 1),
            array(1, 62, 1),
            array(1, 1, 62),
        );
    }

    /**
     * @dataProvider getOverflowIntRanges
     * @expectedException \GpsLab\Component\Base64UID\Exception\ArgumentRangeException
     *
     * @param int $generator_length
     * @param int $sequence_length
     * @param int $time_length
     */
    public function testOverflowIntRanges($generator_length, $sequence_length, $time_length)
    {
        $generator = new SnowflakeGenerator(0, $generator_length, $sequence_length, $time_length);
    }

    /**
     * @return array
     */
    public function getOverflowGenerators()
    {
        return array(
            array(2, 1),
            array(4, 2),
            array(8, 3),
            array(16, 4),
            array(32, 5),
            array(64, 6),
            array(128, 7),
            array(256, 8),
            array(512, 9),
            array(1024, 10),
        );
    }

    /**
     * @dataProvider getOverflowGenerators
     * @expectedException \GpsLab\Component\Base64UID\Exception\ArgumentRangeException
     *
     * @param int $generator
     * @param int $generator_length
     */
    public function testOverflowGenerator($generator, $generator_length)
    {
        $generator = new SnowflakeGenerator($generator, $generator_length);
    }

    /**
     * @expectedException \GpsLab\Component\Base64UID\Exception\ArgumentRangeException
     */
    public function testGrateTimeOffsetForCurrentTime()
    {
        $now = (int) floor(microtime(true) * 1000);
        $generator = new SnowflakeGenerator(0, 10, 12, 41, $now + 100);
    }

    /**
     * @return array
     */
    public function getGenerateParams()
    {
        return array(
            array(0, 10, 12, 41, 0),
            array(0, 10, 12, 41, (int) floor(microtime(true) * 1000) - 1),
            array(0, 8, 10, 45, 0),
        );
    }

    /**
     * @dataProvider getGenerateParams
     *
     * @param int $generator_id
     * @param int $generator_length
     * @param int $sequence_length
     * @param int $time_length
     * @param int $time_offset
     */
    public function testGenerate($generator_id, $generator_length, $sequence_length, $time_length, $time_offset)
    {
        $generator = new SnowflakeGenerator(
            $generator_id,
            $generator_length,
            $sequence_length,
            $time_length,
            $time_offset
        );
        $id = $generator->generate();
        $this->assertInternalType('integer', $id);
    }

    public function testIncrementSequence()
    {
        $generator = new SnowflakeGenerator(0);
        self::assertNotSame($generator->generate(), $generator->generate());
    }
}
