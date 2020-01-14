<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Component\Base64UID\BitmapEncoder;

class HexToBase64BitmapEncoder implements BitmapEncoder
{
    /**
     * @param int $bitmap
     *
     * @return string
     */
    public function encode($bitmap)
    {
        $hex = dechex($bitmap);
        $binary = pack('H*', $hex);
        $base64 = base64_encode($binary);
        $base64 = str_replace('=', '', $base64);

        return $base64;
    }
}
