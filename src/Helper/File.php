<?php

namespace ShadeSoft\GDImage\Helper;

use GdImage;
use ShadeSoft\GDImage\Exception\FileInvalidTypeException;
use ShadeSoft\GDImage\Exception\FileNotFoundException;

class File
{
    public const AVIF = 'image/avif';
    public const BMP = PHP_VERSION_ID >= 70300 ? 'image/bmp' : 'image/x-ms-bmp';
    public const GIF = 'image/gif';
    public const JPG = 'image/jpeg';
    public const PNG = 'image/png';
    public const WEBP = 'image/webp';
    public const FORMATS = [
        'avif'  => self::AVIF,
        'bmp'  => self::BMP,
        'gif'  => self::GIF,
        'jpg'  => self::JPG,
        'jpeg' => self::JPG,
        'png'  => self::PNG,
        'webp' => self::WEBP,
    ];

    /**
     * Get image type based on given format or mime info
     * @throws FileNotFoundException
     */
    public static function getType(string $path, ?string $outputFormat = null, ?array $imgInfo = null): string
    {
        $imgInfo = $imgInfo ?: self::getSize($path);
        $availableFormats = self::FORMATS;

        if ($outputFormat) {
            $type = $availableFormats[$outputFormat] ?? self::JPG;
        } else {
            $type = $imgInfo['mime'];
        }

        return $type;
    }

    /**
     * Get and return PHP's getimagesize data
     * @throws FileNotFoundException
     */
    public static function getSize(string $path): array
    {
        if (!file_exists($path) || is_dir($path)) {
            throw new FileNotFoundException('Image not found.');
        }

        return getimagesize($path);
    }

    /**
     * Make and return an image resource from file
     * @throws FileNotFoundException|FileInvalidTypeException
     */
    public static function get(string $path, array $info = null): GdImage
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
                $srcImg = imagecreatefrombmp($path);
                break;
            case self::WEBP:
                $srcImg = imagecreatefromwebp($path);
                break;
            case self::AVIF:
                if (PHP_VERSION_ID < 80100) {
                    throw new FileInvalidTypeException('Only supported in PHP 8.1 and above');
                }
                $srcImg = imagecreatefromavif($path);
                break;
            default:
                throw new FileInvalidTypeException('Not supported image type.');
        }

        return $srcImg;
    }

    /**
     * Save image to given path
     * @throws FileInvalidTypeException
     */
    public static function save(string $path, GdImage $img, string $type = self::JPG, ?int $quality = null): void
    {
        $dir = explode('/', $path);
        unset($dir[count($dir) - 1]);
        $dir = implode('/', $dir);
        if ($dir && (!file_exists($dir) || !is_dir($dir))) {
            mkdir($dir, 0777, true);
        }

        self::output($path, $img, $type, $quality);
    }

    /**
     * @throws FileInvalidTypeException
     */
    private static function output(string $path, GdImage $img, string $type, ?int $quality): void
    {
        if ($quality === 101 && ($type !== self::WEBP || PHP_VERSION_ID < 80100)) {
            $quality = 100;
        }

        switch ($type) {
            case self::PNG:
                @imagepng(
                    $img,
                    $path,
                    $quality
                        ? ($quality >= 10 ? floor($quality / 10 - 1) : 0)
                        : -1
                );
                break;
            case self::GIF:
                @imagegif($img, $path);
                break;
            case self::BMP:
                @imagebmp($img, $path);
                break;
            case self::WEBP:
                @imagewebp($img, $path, $quality ?: 80);
                break;
            case self::AVIF:
                if (PHP_VERSION_ID < 80100) {
                    throw new FileInvalidTypeException('Only supported in PHP 8.1 and above.');
                }
                @imageavif($img, $path, $quality ?: -1);
                break;
            case self::JPG:
            default:
                @imagejpeg($img, $path, $quality ?: -1);
        }
    }

    /**
     * Print image to PHP output
     * @throws FileInvalidTypeException
     */
    public static function printToOutput(GdImage $img, string $type = self::JPG, ?int $quality = null): void {
        self::output('php://output', $img, $type, $quality);
    }

    /**
     * Clean (destroy) given (by reference) image resources
     */
    public static function clean(array $imgs): void
    {
        foreach ($imgs as $img) {
            imagedestroy($img);
        }
    }
}
