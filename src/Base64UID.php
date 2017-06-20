<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Component\Base64UID;

class Base64UID
{
    const CHARS = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_-';

    /**
     * @param int $length
     *
     * @return string
     */
    public static function generate($length = 10)
    {
        $uid = '';
        while ($length-- > 0) {
            $uid .= self::CHARS[self::random(0, 63)];
        }

        return $uid;
    }

    /**
     * Generates cryptographically secure pseudo-random integers.
     *
     * Follback for PHP < 7.0
     *
     * @see http://php.net/manual/en/function.random-int.php#119670
     *
     * @param int $min
     * @param int $max
     *
     * @return int|null
     */
    private static function random($min, $max)
    {
        if (function_exists('random_int')) {
            return random_int($min, $max);
        }

        if (!function_exists('mcrypt_create_iv')) {
            trigger_error('mcrypt must be loaded for random_int to work', E_USER_WARNING);

            return null;
        }

        $range = $counter = $max - $min;
        $bits = 1;

        while ($counter >>= 1) {
            ++$bits;
        }

        $bytes = (int) max(ceil($bits / 8), 1);
        $bitmask = pow(2, $bits) - 1;

        if ($bitmask >= PHP_INT_MAX) {
            $bitmask = PHP_INT_MAX;
        }

        do {
            $result = hexdec(bin2hex(mcrypt_create_iv($bytes, MCRYPT_DEV_URANDOM))) & $bitmask;
        } while ($result > $range);

        return $result + $min;
    }
}
