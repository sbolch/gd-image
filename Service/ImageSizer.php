<?php

namespace ShadeSoft\GDImage\Service;

use ShadeSoft\GDImage\Exception\FileException;
use ShadeSoft\GDImage\Helper\ImageCache;
use ShadeSoft\GDImage\Helper\ImageFile;
use ShadeSoft\GDImage\Helper\ImageOptions;

class ImageSizer {
    private $cache;

    public function __construct() {
        $this->cache = new ImageCache;
    }

    /**
     * Set an image to the given width while preserving its ratio
     *
     * @param string $img
     * @param int $width
     * @param null|"jpeg"|"jpg"|"png"|"gif"|"wbmp"|"bmp"|"webp" $outputFormat - if null, it won't change
     * @param null|string $targetPath - if null, $img will be used
     * @throws FileException
     */
    public function widen($img, $width, $outputFormat = null, $targetPath = null) {
        if($targetPath && $this->cache->cached($targetPath)) {
            return;
        }

        list($ow, $oh) = $imgInfo = ImageFile::getSize($img);
        $srcImg = ImageFile::get($img, $imgInfo);

        if($width == $ow) {
            if($targetPath) {
                ImageFile::save($targetPath, $srcImg, ImageFile::getType($img, $outputFormat, $imgInfo));
            }
            return;
        }

        // calculate new size
        $nw = $width;
        $nh = round(($nw / $ow) * $oh);

        // save
        $dstImg = $this->resample($srcImg, $nw, $nh, $ow, $oh, $imgInfo);

        ImageFile::save($targetPath ?: $img, $dstImg, ImageFile::getType($img, $outputFormat, $imgInfo));
        ImageFile::clean(array(&$dstImg, &$srcImg));
    }

    /**
     * Set an image to the given height while preserving its ratio
     *
     * @param string $img
     * @param int $height
     * @param null|"jpeg"|"jpg"|"png"|"gif"|"wbmp"|"bmp"|"webp" $outputFormat - if null, it won't change
     * @param null|string $targetPath - if null, $img will be used
     * @throws FileException
     */
    public function heighten($img, $height, $outputFormat = null, $targetPath = null) {
        if($targetPath && $this->cache->cached($targetPath)) {
            return;
        }

        list($ow, $oh) = $imgInfo = ImageFile::getSize($img);
        $srcImg = ImageFile::get($img, $imgInfo);

        if($height == $oh) {
            if($targetPath) {
                ImageFile::save($targetPath, $srcImg, ImageFile::getType($img, $outputFormat, $imgInfo));
            }
            return;
        }

        // calculate new size
        $nh = $height;
        $nw = round(($nh / $oh) * $ow);

        // save
        $dstImg = $this->resample($srcImg, $nw, $nh, $ow, $oh, $imgInfo);

        ImageFile::save($targetPath ?: $img, $dstImg, ImageFile::getType($img, $outputFormat, $imgInfo));
        ImageFile::clean(array(&$dstImg, &$srcImg));
    }

    /**
     * Maximize image's size by its longest dimension while preserving its ratio
     *
     * @param string $img
     * @param int $maxWidth
     * @param int $maxHeight
     * @param null|"jpeg"|"jpg"|"png"|"gif"|"wbmp"|"bmp"|"webp" $outputFormat - if null, it won't change
     * @param null|string $targetPath - if null, $img will be used
     * @throws FileException
     */
    public function maximize($img, $maxWidth, $maxHeight, $outputFormat = null, $targetPath = null) {
        if($targetPath && $this->cache->cached($targetPath)) {
            return;
        }

        list($ow, $oh) = $imgInfo = ImageFile::getSize($img);
        $srcImg = ImageFile::get($img, $imgInfo);

        // calculate new size
        if($maxWidth >= $ow && $maxHeight >= $oh) {
            if($targetPath) {
                ImageFile::save($targetPath, $srcImg, ImageFile::getType($img, $outputFormat, $imgInfo));
            }
            return;
        }

        if($ow >= $oh) {
            $nw = $maxWidth;
            $nh = floor(($nw / $ow) * $oh);
        } else {
            $nh = $maxHeight;
            $nw = floor(($nh / $oh) * $ow);
        }

        if($nw > $maxWidth) {
            $this->widen($img, $maxWidth, $outputFormat, $targetPath);
            return;
        } elseif($nh > $maxHeight) {
            $this->heighten($img, $maxHeight, $outputFormat, $targetPath);
            return;
        }

        // save
        $dstImg = $this->resample($srcImg, $nw, $nh, $ow, $oh, $imgInfo);

        ImageFile::save($targetPath ?: $img, $dstImg, ImageFile::getType($img, $outputFormat, $imgInfo));
        ImageFile::clean(array(&$dstImg, &$srcImg));
    }

    /**
     * Make a thumbnail by cropping the image by its shortest dimension
     *
     * @param string $img
     * @param int $width
     * @param int $height
     * @param null|"jpeg"|"jpg"|"png"|"gif"|"wbmp"|"bmp"|"webp" $outputFormat - if null, it won't change
     * @param null|string $targetPath - if null, $img will be used
     * @throws FileException
     */
    public function thumbnail($img, $width, $height, $outputFormat = null, $targetPath = null) {
        if($targetPath && $this->cache->cached($targetPath)) {
            return;
        }

        list($ow, $oh) = $imgInfo = ImageFile::getSize($img);
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

        ImageFile::save($targetPath ?: $img, $dstImg, ImageFile::getType($img, $outputFormat, $imgInfo));
        ImageFile::clean(array(&$dstImg, &$srcImg));
    }

    /// Customization functions

    /**
     * Get image cache
     *
     * @return ImageCache
     */
    public function getCache() {
        return $this->cache;
    }

    /// Needed private functions

    private function resample($srcImg, $nw, $nh, $ow, $oh, $imgInfo, $nx = 0, $ny = 0, $ox = 0, $oy = 0) {
        return $this->copy($srcImg, $nw, $nh, $ow, $oh, $imgInfo, $nx, $ny, $ox, $oy, true);
    }

    private function copy($srcImg, $nw, $nh, $ow, $oh, $imgInfo, $nx = 0, $ny = 0, $ox = 0, $oy = 0, $resample = false) {
        $dstImg = imagecreatetruecolor($nw, $nh);
        if(in_array($imgInfo['mime'], array('image/png', 'image/gif', 'image/webp'))) {
            ImageOptions::copyTransparency($dstImg, $srcImg);
        }

        if($resample) {
            imagecopyresampled($dstImg, $srcImg, $nx, $ny, $ox, $oy, $nw, $nh, $ow, $oh);
        } else {
            imagecopy($dstImg, $srcImg, $nx, $ny, $ox, $oy, $ow, $oh);
        }

        return $dstImg;
    }
}