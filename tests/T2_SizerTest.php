<?php

use PHPUnit\Framework\TestCase;
use ShadeSoft\GDImage\Sizer;

final class T2_SizerTest extends TestCase
{
    private Sizer $sizer;
    private array $imgs;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->sizer = new Sizer();
        $this->imgs = [
            'horizontal' => __DIR__ . '/img/test-horizontal.jpg',
            'vertical'   => __DIR__ . '/img/test-vertical.jpg',
            'square'     => __DIR__ . '/img/test-square.jpg',
        ];
    }

    public function testWiden()
    {
        foreach ($this->imgs as $type => $img) {
            $testImg = $this->sizer
                ->image($img)
                ->widen(2)
                ->instance();

            $this->assertEquals(2, imagesx($testImg), "Widen fails if image is $type:");
        }
    }

    public function testHeighten()
    {
        foreach ($this->imgs as $type => $img) {
            $testImg = $this->sizer
                ->image($img)
                ->heighten(2)
                ->instance();

            $this->assertEquals(2, imagesy($testImg), "Heighten fails if image is $type:");
        }
    }

    public function testMaximize()
    {
        $sizes = [
            [20, 20],
            [2, 20],
            [20, 2],
            [2, 2],
        ];

        foreach ($sizes as $size) {
            foreach ($this->imgs as $type => $img) {
                $testImg = $this->sizer
                    ->image($img)
                    ->maximize($size[0], $size[1])
                    ->instance();

                $this->assertLessThanOrEqual(
                    $size[0],
                    imagesx($testImg),
                    "Maximize width fails if image is $type with max sizes $size[0] and $size[1]:"
                );
                $this->assertLessThanOrEqual(
                    $size[1],
                    imagesy($testImg),
                    "Maximize height fails if image is $type with max sizes $size[0] and $size[1]:"
                );
            }
        }
    }

    public function testCrop()
    {
        foreach ($this->imgs as $type => $img) {
            $testImg = $this->sizer
                ->image($img)
                ->crop(2, 2)
                ->instance();

            $this->assertEquals(2, imagesy($testImg), "Crop width fails if image is $type:");
            $this->assertEquals(2, imagesy($testImg), "Crop height fails if image is $type:");
        }
    }

    public function testThumbnail()
    {
        foreach ($this->imgs as $type => $img) {
            $testImg = $this->sizer
                ->image($img)
                ->thumbnail(2, 2)
                ->instance();

            $this->assertEquals(2, imagesx($testImg), "Thumbnail width fails if image is $type:");
            $this->assertEquals(2, imagesy($testImg), "Thumbnail height fails if image is $type:");
        }
    }
}
