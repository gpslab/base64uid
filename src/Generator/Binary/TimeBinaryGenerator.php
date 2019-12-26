<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Component\Base64UID\Generator\Binary;

use GpsLab\Component\Base64UID\Exception\ArgumentTypeException;
use GpsLab\Component\Base64UID\Exception\BitmapOverflowException;
use GpsLab\Component\Base64UID\Exception\InvalidArgumentException;
use GpsLab\Component\Base64UID\Exception\SmallBitModeException;
use GpsLab\Component\Base64UID\Exception\ZeroArgumentException;

class TimeBinaryGenerator implements BinaryGenerator
{
    /**
     * @var int
     */
    private $suffix_length;

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
    private $prefix_max_value;

    /**
     * @var int
     */
    private $suffix_max_value;

    /**
     * Bitmap of a random prefix + time + random suffix.
     *
     * The time length defines the limit of the stored date:
     *  40-bits = 1111111111111111111111111111111111111111      = 1099511627775  = 2004-11-03 19:53:47 (UTC)
     *  41-bits = 11111111111111111111111111111111111111111     = 2199023255551  = 2039-09-07 16:47:35 (UTC)
     *  42-bits = 111111111111111111111111111111111111111111    = 4398046511103  = 2109-05-15 08:35:11 (UTC)
     *  43-bits = 1111111111111111111111111111111111111111111   = 8796093022207  = 2248-09-26 16:10:22 (UTC)
     *  44-bits = 11111111111111111111111111111111111111111111  = 17592186044415 = 2527-06-23 07:20:44 (UTC)
     *  45-bits = 111111111111111111111111111111111111111111111 = 35184372088831 = 3084-12-12 12:41:28 (UTC)
     *
     * The time offset allows to move the starting point of time in microseconds,
     * which reduces the size of the stored time:
     *  0             = 1970-01-01 00:00:00 (UTC)
     *  1577833200000 = 2020-01-01 00:00:00 (UTC)
     *
     * @param int $prefix_length
     * @param int $time_length
     * @param int $time_offset
     */
    public function __construct($prefix_length = 9, $time_length = 45, $time_offset = 0)
    {
        if (PHP_INT_SIZE * 8 < 64) {
            throw new SmallBitModeException(sprintf('This generator require 64-bit mode of system. Your system support %d-bit mode.', PHP_INT_SIZE * 8));
        }

        if (!is_int($prefix_length)) {
            throw new ArgumentTypeException(sprintf('Length of prefix for UID should be integer, got "%s" instead.', gettype($prefix_length)));
        }

        if (!is_int($time_length)) {
            throw new ArgumentTypeException(sprintf('Length of time for UID should be integer, got "%s" instead.', gettype($time_length)));
        }

        if (!is_int($time_offset)) {
            throw new ArgumentTypeException(sprintf('Time offset should be integer, got "%s" instead.', gettype($time_offset)));
        }

        if ($prefix_length < 0) {
            throw new ZeroArgumentException(sprintf('Length of prefix for UID should be grate then or equal to "0", got "%d" instead.', $prefix_length));
        }

        if ($time_length < 0) {
            throw new ZeroArgumentException(sprintf('Length of time for UID should be grate then or equal to "0", got "%d" instead.', $time_length));
        }

        if ($time_offset < 0) {
            throw new ZeroArgumentException(sprintf('Time offset should be grate then or equal to "0", got "%d" instead.', $time_offset));
        }

        if ($prefix_length + $time_length > 64 - 1) {
            throw new InvalidArgumentException(sprintf('Length of time and prefix for UID should be less than or equal to "%d", got "%d" instead.', 64 - 1, $prefix_length + $time_length));
        }

        $now = (int) floor(microtime(true) * 1000);

        if ($time_offset > $now) {
            throw new InvalidArgumentException(sprintf('Time offset should be grate then or equal to current time "%d", got "%d" instead.', $now, $time_offset));
        }

        $min_time_length = strlen(decbin($now));

        if ($time_length < $min_time_length - $time_offset) {
            throw new InvalidArgumentException(sprintf('Length of time for UID should be grate then or equal to "%d", got "%d" instead.', $min_time_length - $time_offset, $time_length));
        }

        $this->time_length = $time_length;
        $this->time_offset = $time_offset;
        $this->suffix_length = $time_length - $prefix_length;
        $this->prefix_max_value = bindec(str_repeat('1', $prefix_length));
        $this->suffix_max_value = bindec(str_repeat('1', $this->suffix_length));
    }

    /**
     * @return int
     */
    public function generate()
    {
        $time = ((int) floor(microtime(true) * 1000) - $this->time_offset);

        if ($time >= 1 << $this->time_length) {
            throw new BitmapOverflowException(sprintf('Bitmap for time is overflow of %d bits.', $this->time_length));
        }

        $prefix = random_int(0, $this->prefix_max_value);
        $suffix = random_int(0, $this->suffix_max_value);

        $uid = 1 << 64 - 1; // first bit is a bitmap limiter
        $uid |= $prefix << $this->time_length + $this->suffix_length;
        $uid |= $time << $this->suffix_length;
        $uid |= $suffix;

        return $uid;
    }
}
