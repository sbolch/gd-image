<?php

namespace ShadeSoft\GDImage\Helper;

use ShadeSoft\GDImage\Exception\FileInvalidTypeException;
use ShadeSoft\GDImage\Exception\FileNotFoundException;

class File
{
    const TYPE_BMP  = 'image/bmp';
    const TYPE_GIF  = 'image/gif';
    const TYPE_JPG  = 'image/jpeg';
    const TYPE_PNG  = 'image/png';
    const TYPE_WEBP = 'image/webp';
    const FORMATS   = [
        'bmp'  => 'image/bmp',
        'gif'  => 'image/gif',
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png'  => 'image/png',
        'webp' => 'image/webp'
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
        $imgInfo = $imgInfo ?: self::getSize($path);

        if ($outputFormat) {
            switch ($outputFormat) {
                case 'png': $type = ImageFile::TYPE_PNG; break;
                case 'gif': $type = ImageFile::TYPE_GIF; break;
                case 'bmp': $type = ImageFile::TYPE_BMP; break;
                case 'webp': $type = ImageFile::TYPE_WEBP; break;
                case 'jpeg':
                case 'jpg':
                default: $type = ImageFile::TYPE_JPG;
            }
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
                if (PHP_VERSION_ID < 70200) {
                    throw new FileInvalidTypeException('Only supported in PHP 7.2 and above.');
                }
                $srcImg = imagecreatefrombmp($path);
                break;
            case self::TYPE_WEBP:
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
    public static function save($path, $img, $type = self::TYPE_JPG, $quality = null)
    {
        $dir = explode('/', $path);
        unset($dir[count($dir) - 1]);
        $dir = implode('/', $dir);
        if (!file_exists($dir) || !is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        switch ($type) {
            case self::TYPE_PNG:
                @imagepng($img, $path, $quality ?: 9);
                break;
            case self::TYPE_GIF:
                @imagegif($img, $path);
                break;
            case self::TYPE_BMP:
                if (PHP_VERSION_ID < 70200) {
                    throw new FileInvalidTypeException('Only supported in PHP 7.2 and above.');
                }
                @imagebmp($img, $path);
                break;
            case self::TYPE_WEBP:
                @imagewebp($img, $path, $quality ?: 90);
                break;
            case self::TYPE_JPG:
            default:
                @imagejpeg($img, $path, $quality ?: 90);
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