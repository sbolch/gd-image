<?php

use PHPUnit\Framework\TestCase;
use ShadeSoft\GDImage\Helper\ImageFile;
use ShadeSoft\GDImage\Service\ImageConverter;

final class T3_ImageConverterTest extends TestCase
{
    private $converter;
    private $img;
    private $formats;

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->converter    = new ImageConverter;
        $this->img          = __DIR__ . '/img/test-square.jpg';
        $this->formats      = array(
            'jpeg',
            'jpg',
            'png',
            'gif'
        );
    }

    public function testConvert()
    {
        foreach ($this->formats as $format) {
            $testImg = __DIR__ . "/img/test";
            $this->converter->convert($this->img, $format, $testImg);
            $data = getimagesize($testImg);
            $this->assertEquals(ImageFile::getType($testImg, $format), ImageFile::getType($testImg));
            @unlink($testImg);
        }
    }
}
