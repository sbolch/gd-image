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

        $this->saveImage($img, $nw, $nh, $ow, $oh, $srcImg, $imgInfo, $outputFormat);
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

        $this->saveImage($img, $nw, $nh, $ow, $oh, $srcImg, $imgInfo, $outputFormat);
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

        $this->saveImage($img, $nw, $nh, $ow, $oh, $srcImg, $imgInfo, $outputFormat);
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

        $tmpImg = imagecreatetruecolor($nw, $nh);
        imagecopyresampled($tmpImg, $srcImg, 0, 0, 0, 0, $nw, $nh, $ow, $oh);

        $dstImg = imagecreatetruecolor($width, $height);
        imagecopy($dstImg, $tmpImg, 0, 0, $posX, $posY, $width, $height);

        $img = $targetPath ?: $img;

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

        ImageFile::save($img, $dstImg, $type);

        // clean
        imagedestroy($dstImg);
        imagedestroy($srcImg);

//        $this->saveImage($targetPath ?: $img, $width, $height, $ow, $oh, $srcImg, $imgInfo, $outputFormat, 0, 0, $posX, $posY);
    }

    private function saveImage($img, $nw, $nh, $ow, $oh, $srcImg, $imgInfo, $outputFormat, $dx = 0, $dy = 0, $sx = 0, $sy = 0) {
        $dstImg = imagecreatetruecolor($nw, $nh);
        if($imgInfo['mime'] == 'image/png' || $imgInfo['mime'] == 'image/gif') {
            ImageOptions::copyTransparency($dstImg, $srcImg);
        }
        imagecopyresampled($dstImg, $srcImg, $dx, $dy, $sx, $sy, $nw, $nh, $ow, $oh);

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

        ImageFile::save($img, $dstImg, $type);

        // clean
        imagedestroy($dstImg);
        imagedestroy($srcImg);
    }
}