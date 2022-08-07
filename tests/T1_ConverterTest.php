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

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->converter = new Converter();
        $this->img = __DIR__.'/img/test-square.jpg';
        $this->testImg = __DIR__.'/img/test';
    }

    public function testBmp()
    {
        @unlink($this->testImg);

        $this->converter
            ->image($this->img)
            ->target($this->testImg)
            ->toBmp();

        if (PHP_VERSION_ID < 70200) {
            try {
                $this->converter->save();
                $this->fail('Expected Exception has not been raised.');
            } catch (FileInvalidTypeException $ex) {
                $this->assertEquals('Only supported in PHP 7.2 and above.', $ex->getMessage());
            }
        } else {
            $this->converter->save();
            $this->assertEquals(File::BMP, File::getType($this->testImg));
        }

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

        $this->assertEquals(File::GIF, File::getType($this->testImg));
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

        $this->assertEquals(File::JPG, File::getType($this->testImg));
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

        $this->assertEquals(File::JPG, File::getType($this->testImg));
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

        $this->assertEquals(File::PNG, File::getType($this->testImg));
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

            $this->assertEquals(File::WEBP, File::getType($this->testImg));
            @unlink($this->testImg);
        }
    }

    public function testAvif()
    {
        @unlink($this->testImg);

        $this->converter
            ->image($this->img)
            ->target($this->testImg)
            ->toAvif();

        if (PHP_VERSION_ID < 80100) {
            try {
                $this->converter->save();
                $this->fail('Expected Exception has not been raised.');
            } catch (FileInvalidTypeException $ex) {
                $this->assertEquals('Only supported in PHP 8.1 and above.', $ex->getMessage());
            }
        } else {
            $this->converter->save();
            $this->assertEquals(File::AVIF, File::getType($this->testImg));
        }

        @unlink($this->testImg);
    }

    public function testInvalid()
    {
        $this->expectException(FileInvalidTypeException::class);
        $this->converter->toInvalid();
    }
}
