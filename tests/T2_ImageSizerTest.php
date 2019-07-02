<?php

use PHPUnit\Framework\TestCase;
use ShadeSoft\GDImage\Service\ImageSizer;

final class T2_ImageSizerTest extends TestCase
{
    private $sizer;
    private $imgs;
    private $testImg;

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->sizer    = new ImageSizer;
        $this->imgs     = array(
            'horizontal'    => __DIR__ . '/img/test-horizontal.jpg',
            'vertical'      => __DIR__ . '/img/test-vertical.jpg',
            'square'        => __DIR__ . '/img/test-square.jpg'
        );
        $this->testImg  = __DIR__ . '/img/test.jpg';
    }

    public function testWiden()
    {
        foreach ($this->imgs as $type => $img) {
            @unlink($this->testImg);
            $this->sizer->widen($img, 2, null, $this->testImg);
            list($w, $h) = getimagesize($this->testImg);
            $this->assertEquals(2, $w, "Widen fails if image is {$type}:");
        }

        @unlink($this->testImg);
    }

    public function testHeighten()
    {
        foreach ($this->imgs as $type => $img) {
            @unlink($this->testImg);
            $this->sizer->heighten($img, 2, null, $this->testImg);
            list($w, $h) = getimagesize($this->testImg);
            $this->assertEquals(2, $h, "Heighten fails if image is {$type}:");
        }

        @unlink($this->testImg);
    }

    public function testMaximize()
    {
        $sizes = array(
            array(20, 20),
            array(2, 20),
            array(20, 2),
            array(2, 2)
        );

        foreach ($sizes as $size) {
            foreach ($this->imgs as $type => $img) {
                @unlink($this->testImg);
                $this->sizer->maximize($img, $size[0], $size[1], null, $this->testImg);
                list($w, $h) = getimagesize($this->testImg);
                $this->assertLessThanOrEqual($size[0], $w, "Maximize width fails if image is {$type} with max sizes {$size[0]} and {$size[1]}:");
                $this->assertLessThanOrEqual($size[1], $h, "Maximize height fails if image is {$type} with max sizes {$size[0]} and {$size[1]}:");
            }
        }

        @unlink($this->testImg);
    }

    public function testThumbnail()
    {
        foreach ($this->imgs as $type => $img) {
            @unlink($this->testImg);
            $this->sizer->thumbnail($img, 2, 2, null, $this->testImg);
            list($w, $h) = getimagesize($this->testImg);
            $this->assertEquals(2, $w, "Thumbnail width fails if image is {$type}:");
            $this->assertEquals(2, $h, "Thumbnail height fails if image is {$type}:");
        }

        @unlink($this->testImg);
    }
}
