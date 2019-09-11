<?php

namespace ShadeSoft\GDImage;

use ShadeSoft\GDImage\Exception\FileException;
use ShadeSoft\GDImage\Helper\File;

class Converter
{
    private $img;
    private $format;
    private $path;
    private $quality;

    /**
     * Set source image for conversion
     * @param string|resource $path
     * @return self
     * @throws FileException
     */
    public function image($image)
    {
        $this->img = gettype($image) == 'resource'
            ? $image
            : File::get($image);
        return $this;
    }

    /**
     * Set target path
     * @param string $path
     * @return self
     */
    public function target($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * Set output quality
     * @param int $quality
     * @return self
     */
    public function quality($quality)
    {
        $this->quality = $quality;
        return $this;
    }
}