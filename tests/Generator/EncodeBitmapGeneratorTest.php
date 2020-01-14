<?php

/**
 * GpsLab component.
 *
 * @author    Peter Gribanov <info@peter-gribanov.ru>
 * @copyright Copyright (c) 2011, Peter Gribanov
 * @license   http://opensource.org/licenses/MIT
 */

namespace GpsLab\Component\Base64UID\Tests\Generator;

use GpsLab\Component\Base64UID\BitmapEncoder\BitmapEncoder;
use GpsLab\Component\Base64UID\Generator\Binary\BinaryGenerator;
use GpsLab\Component\Base64UID\Generator\EncodeBitmapGenerator;
use PHPUnit\Framework\TestCase;

class EncodeBitmapGeneratorTest extends TestCase
{
    public function testGenerate()
    {
        /* @var $bitmap_encoder BitmapEncoder|\PHPUnit_Framework_MockObject_MockObject */
        $bitmap_encoder = $this->getMock('\GpsLab\Component\Base64UID\BitmapEncoder\BitmapEncoder');
        /* @var $binary_generator BinaryGenerator|\PHPUnit_Framework_MockObject_MockObject */
        $binary_generator = $this->getMock('\GpsLab\Component\Base64UID\Generator\Binary\BinaryGenerator');
        $generator = new EncodeBitmapGenerator($binary_generator, $bitmap_encoder);

        $uid = '12+aF';
        $bitmap = 01010101010101;
        $binary_generator
            ->expects($this->once())
            ->method('generate')
            ->willReturn($bitmap)
        ;
        $bitmap_encoder
            ->expects($this->once())
            ->method('encode')
            ->with($bitmap)
            ->willReturn($uid)
        ;

        $this->assertSame($uid, $generator->generate());
    }
}
