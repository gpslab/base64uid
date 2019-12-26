<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Component\Base64UID\BitmapEncoder;

interface BitmapEncoder
{
    /**
     * @param int $bitmap
     *
     * @return string
     */
    public function encoder($bitmap);
}
