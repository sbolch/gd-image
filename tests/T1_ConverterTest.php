<?php

use PHPUnit\Framework\TestCase;
use ShadeSoft\GDImage\Converter;
use ShadeSoft\GDImage\Exception\FileInvalidTypeException;
use ShadeSoft\GDImage\Helper\File;

final class T1_ConverterTest extends TestCase
{
    private $converter;
    private $img;
    private $testImg;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->converter = new Converter;
        $this->img       = __DIR__ . '/img/test-square.jpg';
        $this->testImg   = __DIR__ . '/img/test';
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
                $this->assertEquals($ex->getMessage(), 'Only supported in PHP 7.2 and above.');
            }
        } else {
            $this->converter->save();
            $this->assertEquals(File::getType($this->testImg), File::BMP);
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

        $this->assertEquals(File::getType($this->testImg), File::GIF);
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

        $this->assertEquals(File::getType($this->testImg), File::JPG);
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

        $this->assertEquals(File::getType($this->testImg), File::JPG);
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

        $this->assertEquals(File::getType($this->testImg), File::PNG);
        @unlink($this->testImg);
    }

    public function testWebp()
    {
        if(function_exists('imagewebp')) {
            @unlink($this->testImg);

            $this->converter
                ->image($this->img)
                ->target($this->testImg)
                ->toWebp()
                ->save();

            $this->assertEquals(File::getType($this->testImg), File::WEBP);
            @unlink($this->testImg);
        }
    }

    /**
     * @expectedException ShadeSoft\GDImage\Exception\FileInvalidTypeException
     */
    public function testInvalid()
    {
        $this->converter->toInvalid();
    }
}