<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Component\Base64UID\Generator\Binary;

use GpsLab\Component\Base64UID\Exception\ArgumentRangeException;
use GpsLab\Component\Base64UID\Exception\ArgumentTypeException;
use GpsLab\Component\Base64UID\Exception\BitmapOverflowException;
use GpsLab\Component\Base64UID\Exception\ProcessorArchitectureException;
use GpsLab\Component\Base64UID\Exception\ZeroArgumentException;

class SnowflakeGenerator implements BinaryGenerator
{
    /**
     * @var int
     */
    private $generator;

    /**
     * @var int
     */
    private $generator_length;

    /**
     * @var int
     */
    private $sequence_length;

    /**
     * @var int
     */
    private $time_length;

    /**
     * @var int
     */
    private $time_offset;

    /**
     * @var int
     */
    private $last_time = 0;

    /**
     * @var int
     */
    private $sequence = 0;

    /**
     * Snowflake.
     *
     * The time length defines the limit of the stored date:
     *  40-bits = 1111111111111111111111111111111111111111      = 1099511627775  = 2004-11-03 19:53:48 (UTC)
     *  41-bits = 11111111111111111111111111111111111111111     = 2199023255551  = 2039-09-07 15:47:36 (UTC)
     *  42-bits = 111111111111111111111111111111111111111111    = 4398046511103  = 2109-05-15 07:35:11 (UTC)
     *  43-bits = 1111111111111111111111111111111111111111111   = 8796093022207  = 2248-09-26 15:10:22 (UTC)
     *  44-bits = 11111111111111111111111111111111111111111111  = 17592186044415 = 2527-06-23 06:20:44 (UTC)
     *  45-bits = 111111111111111111111111111111111111111111111 = 35184372088831 = 3084-12-12 12:41:29 (UTC)
     *
     * The time offset allows to move the starting point of time in microseconds,
     * which reduces the size of the stored time:
     *  0             = 1970-01-01 00:00:00 (UTC)
     *  1577836800000 = 2020-01-01 00:00:00 (UTC)
     *
     * @param int $generator
     * @param int $generator_length
     * @param int $sequence_length
     * @param int $time_length
     * @param int $time_offset
     */
    public function __construct(
        $generator,
        $generator_length = 10,
        $sequence_length = 12,
        $time_length = 41,
        $time_offset = 1577836800000
    ) {
        // @codeCoverageIgnoreStart
        // can't reproduce this condition in tests
        if (PHP_INT_SIZE * 8 < 64) {
            throw new ProcessorArchitectureException(sprintf('This generator require 64-bit mode of processor architecture. Your processor architecture support %d-bit mode.', PHP_INT_SIZE * 8));
        }
        // @codeCoverageIgnoreEnd

        if (!is_int($generator)) {
            throw new ArgumentTypeException(sprintf('Generator should be integer, got "%s" instead.', gettype($generator)));
        }

        if (!is_int($generator_length)) {
            throw new ArgumentTypeException(sprintf('Generator length should be integer, got "%s" instead.', gettype($generator_length)));
        }

        if (!is_int($sequence_length)) {
            throw new ArgumentTypeException(sprintf('Sequence length should be integer, got "%s" instead.', gettype($sequence_length)));
        }

        if (!is_int($time_length)) {
            throw new ArgumentTypeException(sprintf('Time length should be integer, got "%s" instead.', gettype($time_length)));
        }

        if (!is_int($time_offset)) {
            throw new ArgumentTypeException(sprintf('Time offset should be integer, got "%s" instead.', gettype($time_offset)));
        }

        if ($generator < 0) {
            throw new ZeroArgumentException(sprintf('Generator should be grate then "0", got "%d" instead.', $generator));
        }

        if ($generator_length < 0) {
            throw new ZeroArgumentException(sprintf('Generator length should be grate then "0", got "%d" instead.', $generator_length));
        }

        if ($sequence_length < 0) {
            throw new ZeroArgumentException(sprintf('Sequence length should be grate then "0", got "%d" instead.', $sequence_length));
        }

        if ($time_length < 0) {
            throw new ZeroArgumentException(sprintf('Time length should be grate then "0", got "%d" instead.', $time_length));
        }

        if ($time_offset < 0) {
            throw new ZeroArgumentException(sprintf('Time offset should be grate then "0", got "%d" instead.', $time_offset));
        }

        if ($generator_length + $sequence_length + $time_length > 64 - 1) {
            throw new ArgumentRangeException(sprintf('Length of generator, sequence and time for UID should be less than or equal to "%d", got "%d" instead.', 64 - 1, $generator_length + $sequence_length + $time_length));
        }

        $max_generator_id = (int) bindec(str_repeat('1', $generator_length));

        if ($generator > $max_generator_id) {
            throw new ArgumentRangeException(sprintf('Generator should be grate then or equal to "%d", got "%d" instead.', $max_generator_id, $generator));
        }

        $now = (int) floor(microtime(true) * 1000);

        if ($time_offset > $now) {
            throw new ArgumentRangeException(sprintf('Time offset should be grate then or equal to current time "%d", got "%d" instead.', $now, $time_offset));
        }

        $min_time_length = strlen(decbin($now - $time_offset));

        if ($time_length < $min_time_length) {
            throw new ArgumentRangeException(sprintf('Length of time for UID should be grate then or equal to "%d", got "%d" instead.', $min_time_length, $time_length));
        }

        $this->generator = $generator;
        $this->generator_length = $generator_length;
        $this->sequence_length = $sequence_length;
        $this->time_length = $time_length;
        $this->time_offset = $time_offset;
    }

    /**
     * @return int
     */
    public function generate()
    {
        $time = ((int) floor(microtime(true) * 1000) - $this->time_offset);

        // @codeCoverageIgnoreStart
        // overflow validation is in the constructor,
        // but there is a chance that overflow will occur in the process of using this service
        if ($time >= 1 << $this->time_length) {
            throw new BitmapOverflowException(sprintf('Bitmap for time is overflow of %d bits.', $this->time_length));
        }
        // @codeCoverageIgnoreEnd

        if ($this->last_time === $time) {
            ++$this->sequence;
        } else {
            $this->last_time = $time;
            $this->sequence = 0;
        }

        $uid = 1 << 64 - 1;
        $uid |= $time << $this->generator_length + $this->sequence_length;
        $uid |= $this->generator << $this->sequence_length;
        $uid |= $this->sequence;

        return $uid;
    }
}
