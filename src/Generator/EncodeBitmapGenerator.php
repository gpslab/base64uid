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
use GpsLab\Component\Base64UID\Generator\Binary\BinaryGenerator;

class EncodeBitmapGenerator implements Generator
{
    /**
     * @var BinaryGenerator
     */
    private $generator;

    /**
     * @var BitmapEncoder
     */
    private $encoder;

    /**
     * @param BinaryGenerator $generator
     * @param BitmapEncoder   $encoder
     */
    public function __construct(BinaryGenerator $generator, BitmapEncoder $encoder)
    {
        $this->generator = $generator;
        $this->encoder = $encoder;
    }

    /**
     * @return string
     */
    public function generate()
    {
        return $this->encoder->encoder($this->generator->generate());
    }
}
