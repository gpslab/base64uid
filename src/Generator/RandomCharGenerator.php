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
use GpsLab\Component\Base64UID\Exception\InvalidArgumentException;

class RandomCharGenerator implements Generator
{
    /**
     * @var int
     */
    private $uid_length;

    /**
     * @var string
     */
    private $charset;

    /**
     * @var int
     */
    private $charset_size;

    /**
     * @param int    $uid_length
     * @param string $charset
     */
    public function __construct(
        $uid_length = 10,
        $charset = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_-'
    ) {
        if (!is_int($uid_length)) {
            throw new ArgumentTypeException(sprintf('Length of UID should be integer, got "%s" instead.', gettype($uid_length)));
        }

        if (!is_string($charset)) {
            throw new ArgumentTypeException(sprintf('Charset of UID should be a string, got "%s" instead.', gettype($charset)));
        }

        if ($uid_length <= 0) {
            throw new InvalidArgumentException(sprintf('Length of UID should be grate then 0, got "%d" instead.', $uid_length));
        }

        if ($charset === '') {
            throw new InvalidArgumentException('Charset of UID should not be empty.');
        }

        $this->uid_length = $uid_length;
        $this->charset = $charset;
        $this->charset_size = strlen($charset);
    }

    /**
     * @return string
     */
    public function generate()
    {
        $uid = '';
        for ($i = 0; $i < $this->uid_length; ++$i) {
            $uid .= $this->charset[random_int(0, $this->charset_size - 1)];
        }

        return $uid;
    }
}
