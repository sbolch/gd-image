<?php

namespace ShadeSoft\GDImage\Helper;

use ShadeSoft\GDImage\Exception\FileException;

class ImageFile {
    const
        TYPE_JPG = 'image/jpeg',
        TYPE_PNG = 'image/png',
        TYPE_GIF = 'image/gif',
        TYPE_BMP = 'image/wbmp';

    /**
     * Make and return an image resource from file
     * @param string $path
     * @param array $info
     * @return resource
     * @throws FileException
     */
    public static function get($path, array $info) {
        switch($info['mime']) {
            case self::TYPE_JPG:
                $srcImg = imagecreatefromjpeg($path);
                break;
            case self::TYPE_PNG:
                $srcImg = imagecreatefrompng($path);
                break;
            case self::TYPE_GIF:
                $srcImg = imagecreatefromgif($path);
                break;
            case self::TYPE_BMP:
                $srcImg = imagecreatefromwbmp($path);
                break;
            default:
                throw new FileException('Not supported image type.');
        }

        return $srcImg;
    }

    /**
     * Save image to given path from image resource
     * @param string $path
     * @param resource $img
     * @param string $type
     * @param null|integer $quality
     */
    public static function save($path, $img, $type = self::TYPE_JPG, $quality = null) {
        $xPath = explode('/', $path);
        $dir = '';
        for($i = 0; $i < count($xPath) - 1; $i++) {
            $dir .= $xPath[$i];
        }

        if(!file_exists($dir) || !is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        switch($type) {
            case self::TYPE_PNG:
                @imagepng($img, $path, $quality ?: 9);
                break;
            case self::TYPE_GIF:
                @imagegif($img, $path);
                break;
            case self::TYPE_BMP:
                @imagewbmp($img, $path);
                break;
            case self::TYPE_JPG:
            default:
                @imagejpeg($img, $path, $quality ?: 90);
        }
    }

    /**
     * Cleans (destroys) given (by reference) image resources
     * @param array $imgs
     */
    public static function clean(array $imgs) {
        foreach($imgs as &$img) {
            imagedestroy($img);
        }
    }
}