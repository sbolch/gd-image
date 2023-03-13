<?php

namespace ShadeSoft\GDImage\Helper;

use GdImage;
use ShadeSoft\GDImage\Exception\FileInvalidTypeException;
use ShadeSoft\GDImage\Exception\FileNotFoundException;

class File
{
    public const FORMATS = [
        'avif'  => IMAGETYPE_AVIF,
        'bmp'  => IMAGETYPE_BMP,
        'gif'  => IMAGETYPE_GIF,
        'jpg'  => IMAGETYPE_JPEG,
        'jpeg' => IMAGETYPE_JPEG,
        'png'  => IMAGETYPE_PNG,
        'webp' => IMAGETYPE_WEBP,
    ];

    /**
     * Get image type based on given format or mime info
     * @throws FileNotFoundException
     */
    public static function getType(string $path, ?string $outputFormat = null): int
    {
        $availableFormats = self::FORMATS;

        if ($outputFormat) {
            $type = $availableFormats[$outputFormat] ?? IMAGETYPE_JPEG;
        } else {
            if (($type = exif_imagetype($path)) === false) {
                throw new FileNotFoundException('Image not found.');
            }
        }

        return $type;
    }

    /**
     * Make and return an image resource from file
     * @throws FileNotFoundException|FileInvalidTypeException
     */
    public static function get(string $path): GdImage
    {
        if (($type = exif_imagetype($path)) === false) {
            throw new FileNotFoundException('Image not found.');
        }

        self::checkSupport($type);

        return match ($type) {
            IMAGETYPE_JPEG => imagecreatefromjpeg($path),
            IMAGETYPE_PNG => imagecreatefrompng($path),
            IMAGETYPE_GIF => imagecreatefromgif($path),
            IMAGETYPE_BMP => imagecreatefrombmp($path),
            IMAGETYPE_WEBP => imagecreatefromwebp($path),
            IMAGETYPE_AVIF => imagecreatefromavif($path),
            default => throw new FileInvalidTypeException('Not supported image type.')
        };
    }

    /**
     * Save image to given path
     * @throws FileInvalidTypeException
     */
    public static function save(string $path, GdImage $img, int $type = IMAGETYPE_JPEG, ?int $quality = null): void
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
     * Print image to PHP output
     * @throws FileInvalidTypeException
     */
    public static function printToOutput(GdImage $img, int $type = IMAGETYPE_JPEG, ?int $quality = null): void
    {
        self::output('php://output', $img, image_type_to_mime_type($type), $quality);
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

    /**
     * @throws FileInvalidTypeException
     */
    private static function output(string $path, GdImage $img, int $type, ?int $quality): void
    {
        if ($quality === 101 && $type !== IMAGETYPE_WEBP) {
            $quality = 100;
        }

        self::checkSupport($type);

        switch ($type) {
            case IMAGETYPE_PNG:
                @imagepng(
                    $img,
                    $path,
                    $quality
                        ? ($quality >= 10 ? floor($quality / 10 - 1) : 0)
                        : -1
                );
                break;
            case IMAGETYPE_GIF:
                @imagegif($img, $path);
                break;
            case IMAGETYPE_BMP:
                @imagebmp($img, $path);
                break;
            case IMAGETYPE_WEBP:
                @imagewebp($img, $path, $quality ?: 80);
                break;
            case IMAGETYPE_AVIF:
                @imageavif($img, $path, $quality ?: -1);
                break;
            case IMAGETYPE_JPEG:
            default:
                @imagejpeg($img, $path, $quality ?: -1);
        }
    }

    /**
     * @throws FileInvalidTypeException
     */
    private static function checkSupport(int $type): void
    {
        switch ($type) {
            case IMAGETYPE_JPEG:
                if (!(imagetypes() & IMG_JPG)) {
                    throw new FileInvalidTypeException('JPG support is not enabled.');
                }
                break;
            case IMAGETYPE_PNG:
                if (!(imagetypes() & IMG_PNG)) {
                    throw new FileInvalidTypeException('PNG support is not enabled.');
                }
                break;
            case IMAGETYPE_GIF:
                if (!(imagetypes() & IMG_GIF)) {
                    throw new FileInvalidTypeException('GIF support is not enabled.');
                }
                break;
            case IMAGETYPE_BMP:
                if (!(imagetypes() & IMG_BMP)) {
                    throw new FileInvalidTypeException('BMP support is not enabled.');
                }
                break;
            case IMAGETYPE_WEBP:
                if (!(imagetypes() & IMG_WEBP)) {
                    throw new FileInvalidTypeException('WebP support is not enabled.');
                }
                break;
            case IMAGETYPE_AVIF:
                if (!(imagetypes() & IMG_AVIF)) {
                    throw new FileInvalidTypeException('AVIF support is not enabled.');
                }
                break;
            default:
                throw new FileInvalidTypeException('Not supported image type.');
        }
    }
}
