<?php

use PHPUnit\Framework\TestCase;
use ShadeSoft\GDImage\Exception\FileNotFoundException;
use ShadeSoft\GDImage\Helper\ImageFile;

final class T1_ImageFileTest extends TestCase
{
    private $paths;

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->paths = array(
            __DIR__ . '/img/test-square.jpg',
            __DIR__ . '/img/test-noexist',
            __DIR__ . '/img'
        );
    }

    public function testGetSize()
    {
        $this->assertEquals(ImageFile::getSize($this->paths[0]), getimagesize($this->paths[0]));

        try {
            ImageFile::getSize($this->paths[1]);
            $this->fail('Expected FileNotFoundException has not been raised.');
        } catch (\Exception $ex) {
            $this->assertEquals(get_class($ex), FileNotFoundException::class);
        }

        try {
            ImageFile::getSize($this->paths[2]);
            $this->fail('Expected FileNotFoundException has not been raised.');
        } catch (\Exception $ex) {
            $this->assertEquals(get_class($ex), FileNotFoundException::class);
        }
    }

    public function testGetType()
    {
        $this->assertEquals(ImageFile::getType($this->paths[0]), ImageFile::TYPE_JPG);

        try {
            ImageFile::getType($this->paths[1]);
            $this->fail('Expected FileNotFoundException has not been raised.');
        } catch (\Exception $ex) {
            $this->assertEquals(get_class($ex), FileNotFoundException::class);
        }

        try {
            ImageFile::getType($this->paths[2]);
            $this->fail('Expected FileNotFoundException has not been raised.');
        } catch (\Exception $ex) {
            $this->assertEquals(get_class($ex), FileNotFoundException::class);
        }
    }

    public function testGet()
    {
        $this->assertInternalType('resource', ImageFile::get($this->paths[0]));

        try {
            ImageFile::get($this->paths[1]);
            $this->fail('Expected FileNotFoundException has not been raised.');
        } catch (\Exception $ex) {
            $this->assertEquals(get_class($ex), FileNotFoundException::class);
        }

        try {
            ImageFile::get($this->paths[2]);
            $this->fail('Expected FileNotFoundException has not been raised.');
        } catch (\Exception $ex) {
            $this->assertEquals(get_class($ex), FileNotFoundException::class);
        }
    }
}