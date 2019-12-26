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
    /**
     * TODO use private const after drop PHP < 7.1.
     *
     * @var string
     */
    private static $DEFAULT_CHARSET = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_-';

    /**
     * @param int    $length
     * @param string $charset
     *
     * @return string
     */
    public static function generate($length = 10, $charset = '')
    {
        $charset = $charset ?: self::$DEFAULT_CHARSET;
        $charset_size = strlen($charset);
        $uid = '';
        while ($length-- > 0) {
            $uid .= $charset[random_int(0, $charset_size - 1)];
        }

        return $uid;
    }
}
