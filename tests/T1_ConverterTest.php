<?php

use PHPUnit\Framework\TestCase;
use ShadeSoft\GDImage\Converter;
use ShadeSoft\GDImage\Exception\FileInvalidTypeException;
use ShadeSoft\GDImage\Helper\File;

final class T1_ConverterTest extends TestCase
{
    private Converter $converter;
    private string $img;
    private string $testImg;

    public function __construct($name = null)
    {
        parent::__construct($name);

        $this->converter = new Converter();
        $this->img = __DIR__ . '/img/test-square.jpg';
        $this->testImg = __DIR__ . '/img/test';
    }

    public function testBmp()
    {
        @unlink($this->testImg);

        $this->converter
            ->image($this->img)
            ->target($this->testImg)
            ->toBmp()
            ->save();

        $this->assertEquals(IMAGETYPE_BMP, File::getType($this->testImg));
        @unlink($this->testImg);
    }

    public function testGif()
    {
        @unlink($this->testImg);

        $this->converter
            ->image($this->img)
            ->target($this->testImg)
            ->toGif()
            ->save();

        $this->assertEquals(IMAGETYPE_GIF, File::getType($this->testImg));
        @unlink($this->testImg);
    }

    public function testJpg()
    {
        @unlink($this->testImg);

        $this->converter
            ->image($this->img)
            ->target($this->testImg)
            ->toJpg()
            ->save();

        $this->assertEquals(IMAGETYPE_JPEG, File::getType($this->testImg));
        @unlink($this->testImg);
    }

    public function testJpeg()
    {
        @unlink($this->testImg);

        $this->converter
            ->image($this->img)
            ->target($this->testImg)
            ->toJpeg()
            ->save();

        $this->assertEquals(IMAGETYPE_JPEG, File::getType($this->testImg));
        @unlink($this->testImg);
    }

    public function testPng()
    {
        @unlink($this->testImg);

        $this->converter
            ->image($this->img)
            ->target($this->testImg)
            ->toPng()
            ->save();

        $this->assertEquals(IMAGETYPE_PNG, File::getType($this->testImg));
        @unlink($this->testImg);
    }

    public function testWebp()
    {
        if (function_exists('imagewebp')) {
            @unlink($this->testImg);

            $this->converter
                ->image($this->img)
                ->target($this->testImg)
                ->toWebp()
                ->save();

            $this->assertEquals(IMAGETYPE_WEBP, File::getType($this->testImg));
            @unlink($this->testImg);
        }
    }

    public function testAvif()
    {
        if (function_exists('imageavif')) {
            @unlink($this->testImg);

            $this->converter
                ->image($this->img)
                ->target($this->testImg)
                ->toAvif()
                ->save();

            $this->assertEquals(IMAGETYPE_AVIF, File::getType($this->testImg));
            @unlink($this->testImg);
        }
    }

    public function testInvalid()
    {
        $this->expectException(FileInvalidTypeException::class);
        $this->converter->toInvalid();
    }
}
