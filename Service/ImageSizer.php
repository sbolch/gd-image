<?php

namespace ShadeSoft\GDImage\Service;

use ShadeSoft\GDImage\Helper\ImageFile;
use ShadeSoft\GDImage\Helper\ImageOptions;

class ImageSizer {

    /**
     * Set an image to the given width while preserving its ratio
     *
     * @param $img
     * @param $width
     * @param null|"jpeg"|"jpg"|"png"|"gif"|"wbmp"|"bmp" $outputFormat - if null, it won't change
     */
    public function widen($img, $width, $outputFormat = null) {
        list($ow, $oh) = $imgInfo = getimagesize($img);
        $srcImg = ImageFile::get($img, $imgInfo);

        if($width == $ow) {
            return;
        }

        // calculate new size
        $nw = $width;
        $nh = round(($nw / $ow) * $oh);

        // save
        $dstImg = $this->resample($srcImg, $nw, $nh, $ow, $oh, $imgInfo);

        ImageFile::save($img, $dstImg, $this->getType($outputFormat, $imgInfo));

        $this->clean(array(&$dstImg, &$srcImg));
    }

    /**
     * Set an image to the given height while preserving its ratio
     *
     * @param $img
     * @param $height
     * @param null|"jpeg"|"jpg"|"png"|"gif"|"wbmp"|"bmp" $outputFormat - if null, it won't change
     */
    public function heighten($img, $height, $outputFormat = null) {
        list($ow, $oh) = $imgInfo = getimagesize($img);
        $srcImg = ImageFile::get($img, $imgInfo);

        if($height == $oh) {
            return;
        }

        // calculate new size
        $nh = $height;
        $nw = round(($nh / $oh) * $ow);

        // save
        $dstImg = $this->resample($srcImg, $nw, $nh, $ow, $oh, $imgInfo);

        ImageFile::save($img, $dstImg, $this->getType($outputFormat, $imgInfo));

        $this->clean(array(&$dstImg, &$srcImg));
    }

    /**
     * Maximize image's size by its longest dimension while preserving its ratio
     *
     * @param $img
     * @param $maxWidth
     * @param $maxHeight
     * @param null|"jpeg"|"jpg"|"png"|"gif"|"wbmp"|"bmp" $outputFormat - if null, it won't change
     */
    public function maximize($img, $maxWidth, $maxHeight, $outputFormat = null) {
        list($ow, $oh) = $imgInfo = getimagesize($img);
        $srcImg = ImageFile::get($img, $imgInfo);

        // calculate new size
        if(($ow > $maxWidth && $oh > $maxHeight && $ow >= $oh) || $ow > $maxWidth) {
            $nw = $maxWidth;
            $nh = round(($nw / $ow) * $oh);
        } elseif(($ow > $maxWidth && $oh > $maxHeight && $ow < $oh) || $oh > $maxHeight) {
            $nh = $maxHeight;
            $nw = round(($nh / $oh) * $ow);
        } else {
            return;
        }

        // save
        $dstImg = $this->resample($srcImg, $nw, $nh, $ow, $oh, $imgInfo);

        ImageFile::save($img, $dstImg, $this->getType($outputFormat, $imgInfo));

        $this->clean(array(&$dstImg, &$srcImg));
    }

    /**
     * Make a thumbnail by cropping the image by its shortest dimension
     *
     * @param $img
     * @param $width
     * @param $height
     * @param null|"jpeg"|"jpg"|"png"|"gif"|"wbmp"|"bmp" $outputFormat - if null, it won't change
     * @param null|string $targetPath - if null, $img will be used
     */
    public function thumbnail($img, $width, $height, $outputFormat = null, $targetPath = null) {
        list($ow, $oh) = $imgInfo = getimagesize($img);
        $srcImg = ImageFile::get($img, $imgInfo);

        // calculate new size
        $or = $ow / $oh;
        $nr = $width / $height;

        if($or > $nr) {
            $nh = $height;
            $nw = round($nh * $or);
        } else {
            $nw = $width;
            $nh = round($nw / $or);
        }

        $posX = ($nw - $width) / 2;
        $posY = ($nh - $height) / 2;

        // save
        $tmpImg = $this->resample($srcImg, $nw, $nh, $ow, $oh, $imgInfo);
        $dstImg = $this->copy($tmpImg, $width, $height, $width, $height, $imgInfo, 0, 0, $posX, $posY);

        $img = $targetPath ?: $img;

        ImageFile::save($img, $dstImg, $this->getType($outputFormat, $imgInfo));

        $this->clean(array(&$dstImg, &$srcImg));
    }

    /// Needed private functions

    private function resample($srcImg, $nw, $nh, $ow, $oh, $imgInfo, $nx = 0, $ny = 0, $ox = 0, $oy = 0) {
        $dstImg = imagecreatetruecolor($nw, $nh);
        if($imgInfo['mime'] == 'image/png' || $imgInfo['mime'] == 'image/gif') {
            ImageOptions::copyTransparency($dstImg, $srcImg);
        }
        imagecopyresampled($dstImg, $srcImg, $nx, $ny, $ox, $oy, $nw, $nh, $ow, $oh);

        return $dstImg;
    }

    private function copy($srcImg, $nw, $nh, $ow, $oh, $imgInfo, $nx = 0, $ny = 0, $ox = 0, $oy = 0) {
        $dstImg = imagecreatetruecolor($nw, $nh);
        if($imgInfo['mime'] == 'image/png' || $imgInfo['mime'] == 'image/gif') {
            ImageOptions::copyTransparency($dstImg, $srcImg);
        }
        imagecopy($dstImg, $srcImg, $nx, $ny, $ox, $oy, $ow, $oh);

        return $dstImg;
    }

    private function getType($outputFormat, $imgInfo) {
        if($outputFormat) {
            switch($outputFormat) {
                case 'png': $type = ImageFile::TYPE_PNG; break;
                case 'gif': $type = ImageFile::TYPE_GIF; break;
                case 'wbmp':
                case 'bmp': $type = ImageFile::TYPE_BMP; break;
                case 'jpeg':
                case 'jpg':
                default: $type = ImageFile::TYPE_JPG;
            }
        } else {
            $type = $imgInfo['mime'];
        }

        return $type;
    }

    private function clean(array $resources) {
        foreach($resources as &$resource) {
            imagedestroy($resource);
        }
    }
}