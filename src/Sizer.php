<?php

namespace sbolch\GDImage;

use GdImage;
use sbolch\GDImage\Exception\FileException;
use sbolch\GDImage\Helper\Options;

class Sizer extends Converter
{
    /**
     * Maximize image's size by its longer dimension while preserving its ratio
     */
    public function maximize(int $width, int $height): self
    {
        [$ow, $oh] = $this->getDimensions();

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

    private function getDimensions(): array
    {
        return [imagesx($this->img), imagesy($this->img)];
    }

    /**
     * Set the image to the given width while preserving its ratio
     */
    public function widen(int $width): self
    {
        [$ow, $oh] = $this->getDimensions();

        if ($width != $ow) {
            $height = round(($width / $ow) * $oh);
            $this->img = $this->resample($width, $height, $ow, $oh);
        }

        return $this;
    }

    private function resample(int $nw, int $nh, int $ow, int $oh): GdImage|bool
    {
        return $this->copy($nw, $nh, $ow, $oh, 0, 0, true);
    }

    private function copy(int $nw, int $nh, int $ow, int $oh, int $x = 0, int $y = 0, bool $resample = false): GdImage|bool
    {
        $dstImg = imagecreatetruecolor($nw, $nh);

        if (in_array($this->originalFormat, [IMAGETYPE_AVIF, IMAGETYPE_GIF, IMAGETYPE_PNG, IMAGETYPE_WEBP])
           && in_array($this->format, [IMAGETYPE_AVIF, IMAGETYPE_GIF, IMAGETYPE_PNG, IMAGETYPE_WEBP])) {
            Options::transparency($dstImg, $this->format);
        }

        if ($resample) {
            imagecopyresampled($dstImg, $this->img, 0, 0, $x, $y, $nw, $nh, $ow, $oh);
        } else {
            imagecopy($dstImg, $this->img, 0, 0, $x, $y, $ow, $oh);
        }

        return $dstImg;
    }

    /**
     * Set the image to the given height while preserving its ratio
     */
    public function heighten(int $height): self
    {
        [$ow, $oh] = $this->getDimensions();

        if ($height != $oh) {
            $width = round(($height / $oh) * $ow);
            $this->img = $this->resample($width, $height, $ow, $oh);
        }

        return $this;
    }

    /**
     * Crop picture to given dimensions starting at the given position
     */
    public function crop(int $width, int $height, int $x = 0, int $y = 0): self
    {
        [$ow, $oh] = $this->getDimensions();

        $this->img = $this->copy($width, $height, $ow, $oh, $x, $y);

        return $this;
    }

    /**
     * Make a thumbnail by cropping the image by its shorter dimension (centered crop)
     */
    public function thumbnail(int $width, int $height): self
    {
        [$ow, $oh] = $this->getDimensions();

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
        $this->img = $this->copy(
            $width,
            $height,
            $width,
            $height,
            round(($nw - $width) / 2),
            round(($nh - $height) / 2)
        );

        return $this;
    }

    /**
     * Set source image
     * @throws FileException
     * @see Converter::image()
     */
    public function image(string|GdImage $image): self
    {
        return parent::image($image);
    }

    /**
     * Return the stored instance
     */
    public function instance(): GdImage
    {
        return $this->img;
    }
}
