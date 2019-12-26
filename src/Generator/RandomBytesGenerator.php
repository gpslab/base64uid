<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Component\Base64UID\Generator;

use GpsLab\Component\Base64UID\Exception\ArgumentTypeException;
use GpsLab\Component\Base64UID\Exception\ZeroArgumentException;

class RandomBytesGenerator implements Generator
{
    /**
     * @var int
     */
    private $bytes_length;

    /**
     * @param int $bytes_length
     */
    public function __construct($bytes_length = 8)
    {
        if (!is_int($bytes_length)) {
            throw new ArgumentTypeException(sprintf('Length of bytes should be integer, got "%s" instead.', gettype($bytes_length)));
        }

        if ($bytes_length <= 0) {
            throw new ZeroArgumentException(sprintf('Length of bytes should be grate then 0, got "%d" instead.', $bytes_length));
        }

        $this->bytes_length = $bytes_length;
    }

    /**
     * @return string
     */
    public function generate()
    {
        $uid = random_bytes($this->bytes_length);
        $uid = base64_encode($uid);
        $uid = str_replace('=', '', $uid);

        return $uid;
    }
}
