<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Component\Base64UID\Generator;

use GpsLab\Component\Base64UID\BitmapEncoder\BitmapEncoder;
use GpsLab\Component\Base64UID\Exception\BitmapOverflowException;
use GpsLab\Component\Base64UID\Exception\InvalidArgumentException;
use GpsLab\Component\Base64UID\Exception\SmallBitModeException;

class TimeBinaryGenerator implements BinaryGenerator, Generator
{
    /**
     * @var BitmapEncoder
     */
    private $encoder;

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
     * @param BitmapEncoder $encoder
     * @param int           $prefix_length
     * @param int           $time_length
     */
    public function __construct(BitmapEncoder $encoder, $prefix_length = 9, $time_length = 45)
    {
        if (PHP_INT_SIZE * 8 < 64) {
            throw new SmallBitModeException(sprintf('This generator require 64-bit mode of system. Your system support %d-bit mode.', PHP_INT_SIZE * 8));
        }
        if (!is_int($prefix_length)) {
            throw new InvalidArgumentException(sprintf('Length of prefix for UID should be integer, got "%s" instead.', gettype($prefix_length)));
        }
        if (!is_int($time_length)) {
            throw new InvalidArgumentException(sprintf('Length of time for UID should be integer, got "%s" instead.', gettype($time_length)));
        }
        if ($prefix_length < 0) {
            throw new InvalidArgumentException(sprintf('Length of prefix for UID should be grate then or equal to "0", got "%d" instead.', $prefix_length));
        }
        if ($time_length < 0) {
            throw new InvalidArgumentException(sprintf('Length of time for UID should be grate then or equal to "0", got "%d" instead.', $time_length));
        }
        if ($prefix_length + $time_length > 64 - 1) {
            throw new InvalidArgumentException(sprintf('Length of time and prefix for UID should be less than or equal to "%d", got "%d" instead.', 64 - 1, $prefix_length + $time_length));
        }
        $min_time_length = strlen(decbin((int) floor(microtime(true) * 1000)));
        if ($time_length < $min_time_length) {
            throw new InvalidArgumentException(sprintf('Length of time for UID should be grate then or equal to "%d", got "%d" instead.', $min_time_length, $prefix_length));
        }

        $this->encoder = $encoder;
        $this->time_length = $time_length;
        $this->suffix_length = $time_length - $prefix_length;

        $this->prefix_max_value = 0;
        for ($i = 0; $i < $prefix_length; ++$i) {
            $this->prefix_max_value |= 1 << $i;
        }

        $this->suffix_max_value = 0;
        for ($i = 0; $i < $this->suffix_length; ++$i) {
            $this->suffix_max_value |= 1 << $i;
        }
    }

    /**
     * @return int
     */
    public function generateBitmap()
    {
        $time = (int) floor(microtime(true) * 1000);

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

    /**
     * @return string
     */
    public function generate()
    {
        return $this->encoder->encoder($this->generateBitmap());
    }
}
