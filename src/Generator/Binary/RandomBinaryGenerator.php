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
use GpsLab\Component\Base64UID\Exception\ZeroArgumentException;

class RandomBinaryGenerator implements BinaryGenerator
{
    /**
     * @var int
     */
    private $uid_bitmap_length;

    /**
     * @param int $uid_bitmap_length
     */
    public function __construct($uid_bitmap_length)
    {
        if (!is_int($uid_bitmap_length)) {
            throw new ArgumentTypeException(sprintf('Length of bitmap for UID should be integer, got "%s" instead.', gettype($uid_bitmap_length)));
        }

        if ($uid_bitmap_length <= 0) {
            throw new ZeroArgumentException(sprintf('Length of bitmap for UID should be grate then 0, got "%d" instead.', $uid_bitmap_length));
        }

        $this->uid_bitmap_length = $uid_bitmap_length;
    }

    /**
     * @return int
     */
    public function generate()
    {
        $uid = 0;
        for ($i = 0; $i < $this->uid_bitmap_length; ++$i) {
            if (random_int(0, 1)) {
                $uid |= 1 << $i;
            }
        }

        return $uid;
    }
}
