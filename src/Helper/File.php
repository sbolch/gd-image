<?php

namespace ShadeSoft\GDImage\Helper;

use ShadeSoft\GDImage\Exception\FileInvalidTypeException;
use ShadeSoft\GDImage\Exception\FileNotFoundException;

class File
{
    const BMP     = PHP_VERSION_ID >= 70300 ? 'image/bmp' : 'image/x-ms-bmp';
    const GIF     = 'image/gif';
    const JPG     = 'image/jpeg';
    const PNG     = 'image/png';
    const WEBP    = 'image/webp';
    const FORMATS = [
        'bmp'  => self::BMP,
        'gif'  => self::GIF,
        'jpg'  => self::JPG,
        'jpeg' => self::JPG,
        'png'  => self::PNG,
        'webp' => self::WEBP
    ];

    /**
     * Get and return PHP's getimagesize data
     * @param string $path
     * @return array
     * @throws FileNotFoundException
     */
    public static function getSize($path)
    {
        if (!file_exists($path) || is_dir($path)) {
            throw new FileNotFoundException('Image not found');
        }

        return getimagesize($path);
    }

    /**
     * Get image type based on given format or mime info
     * @param null|string $outputFormat - if null, mime info is used
     * @param null|array $imgInfo
     * @return string
     */
    public static function getType($path, $outputFormat = null, array $imgInfo = null)
    {
        $imgInfo          = $imgInfo ?: self::getSize($path);
        $availableFormats = self::FORMATS;

        if ($outputFormat) {
            $type = isset($availableFormats[$outputFormat])
                ? $availableFormats[$outputFormat]
                : self::JPG;
        } else {
            $type = $imgInfo['mime'];
        }

        return $type;
    }

    /**
     * Make and return an image resource from file
     * @param string $path
     * @param array $info
     * @return resource
     * @throws FileInvalidTypeException
     */
    public static function get($path, array $info = null)
    {
        $info = $info ?: self::getSize($path);

        switch ($info['mime']) {
            case self::JPG:
                $srcImg = imagecreatefromjpeg($path);
                break;
            case self::PNG:
                $srcImg = imagecreatefrompng($path);
                break;
            case self::GIF:
                $srcImg = imagecreatefromgif($path);
                break;
            case self::BMP:
                if (PHP_VERSION_ID < 70200) {
                    throw new FileInvalidTypeException('Only supported in PHP 7.2 and above.');
                }
                $srcImg = imagecreatefrombmp($path);
                break;
            case self::WEBP:
                $srcImg = imagecreatefromwebp($path);
                break;
            default:
                throw new FileInvalidTypeException('Not supported image type.');
        }

        return $srcImg;
    }

    /**
     * Save image to given path from image resource
     * @param string $path
     * @param resource $img
     * @param string $type
     * @param null|int $quality
     * @throws FileInvalidTypeException
     */
    public static function save($path, $img, $type = self::JPG, $quality = null)
    {
        $dir = explode('/', $path);
        unset($dir[count($dir) - 1]);
        $dir = implode('/', $dir);
        if (!file_exists($dir) || !is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        switch ($type) {
            case self::PNG:
                @imagepng($img, $path, $quality
                    ? ($quality >= 10 ? floor($quality / 10 - 1) : 0)
                    : -1
                );
                break;
            case self::GIF:
                @imagegif($img, $path);
                break;
            case self::BMP:
                if (PHP_VERSION_ID < 70200) {
                    throw new FileInvalidTypeException('Only supported in PHP 7.2 and above.');
                }
                @imagebmp($img, $path);
                break;
            case self::WEBP:
                @imagewebp($img, $path, $quality ?: 80);
                break;
            case self::JPG:
            default:
                @imagejpeg($img, $path, $quality ?: -1);
        }
    }

    /**
     * Clean (destroy) given (by reference) image resources
     * @param array $imgs
     */
    public static function clean(array $imgs)
    {
        foreach ($imgs as &$img) {
            imagedestroy($img);
        }
    }
}
