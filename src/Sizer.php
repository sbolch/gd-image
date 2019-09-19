<?php

namespace ShadeSoft\GDImage;

use ShadeSoft\GDImage\Exception\FileException;
use ShadeSoft\GDImage\Helper\File;
use ShadeSoft\GDImage\Helper\Options;

class Sizer extends Converter
{

    /**
     * Set the image to the given width while preserving its ratio
     * @param int $width
     * @return self
     */
    public function widen($width)
    {
        list($ow, $oh) = $this->getDimensions();

        if ($width != $ow) {
            $height = round(($width / $ow) * $oh);
            $this->img = $this->resample($width, $height, $ow, $oh);
        }

        return $this;
    }

    /**
     * Set the image to the given height while preserving its ratio
     * @param int $height
     * @return self
     */
    public function heighten($height)
    {
        list($ow, $oh) = $this->getDimensions();

        if ($height != $oh) {
            $width = round(($height / $oh) * $ow);
            $this->img = $this->resample($width, $height, $ow, $oh);
        }

        return $this;
    }

    /**
     * Maximize image's size by its longer dimension while preserving its ratio
     * @param int $width
     * @param int $height
     * @return self
     */
    public function maximize($width, $height)
    {
        list($ow, $oh) = $this->getDimensions();

        if ($width < $ow || $height < $oh) {
            if ($ow >= $oh) {
                $nw = $width;
                $nh = floor(($nw / $ow) * $oh);
            } else {
                $nh = $height;
                $nw = floor(($nh / $oh) * $ow);
            }

            if ($nw > $width) {
                return $this->widen($width);
            } elseif ($nh > $height) {
                return $this->heighten($height);
            }

            $this->img = $this->resample($nw, $nh, $ow, $oh);
        }

        return $this;
    }

    /**
     * Crop picture to given dimensions starting at the given position
     * @param int $width
     * @param int $height
     * @param int $x
     * @param int $y
     * @return self
     */
    public function crop($width, $height, $x = 0, $y = 0)
    {
        list($ow, $oh) = $this->getDimensions();

        $this->img = $this->copy($width, $height, $ow, $oh, $x, $y);

        return $this;
    }

    /**
     * Make a thumbnail by cropping the image by its shorter dimension (centered crop)
     * @param int $width
     * @param int $height
     * @return self
     */
    public function thumbnail($width, $height)
    {
        list($ow, $oh) = $this->getDimensions();

        $or = $ow / $oh;
        $nr = $width / $height;

        if ($nr >= $or) {
            $nw = $width;
            $nh = round($nw / $or);
        } else {
            $nh = $height;
            $nw = round($nh * $or);
        }

        $this->img = $this->resample($nw, $nh, $ow, $oh);
        $this->img = $this->copy($width, $height, $width, $height,
            round(($nw - $width) / 2),
            round(($nh - $height) / 2)
        );

        return $this;
    }

    /**
     * Set source image for conversion or return the stored instance
     * @param null|string|resource $image
     * @return resource|self
     * @throws FileException
     * @see Converter::image()
     */
    public function image($image = null)
    {
        if (!$image) {
            return $this->img;
        }

        return parent::image($image);
    }

    /**
     * Print image to PHP output
     */
    public function output()
    {
        File::printToOutput($this->img, $this->format, $this->quality);
    }

    private function resample($nw, $nh, $ow, $oh)
    {
        return $this->copy($nw, $nh, $ow, $oh, 0, 0, true);
    }

    private function copy($nw, $nh, $ow, $oh, $x = 0, $y = 0, $resample = false)
    {
        $dstImg = imagecreatetruecolor($nw, $nh);

        if (in_array($this->originalFormat, [File::PNG, File::GIF, File::WEBP])) {
            Options::transparency($dstImg, $this->img);
        }

        if ($resample) {
            imagecopyresampled($dstImg, $this->img, 0, 0, $x, $y, $nw, $nh, $ow, $oh);
        } else {
            imagecopy($dstImg, $this->img, 0, 0, $x, $y, $ow, $oh);
        }

        return $dstImg;
    }

    private function getDimensions()
    {
        return [imagesx($this->img), imagesy($this->img)];
    }
}
