<?php

namespace ShadeSoft\GDImage\Service;

use ShadeSoft\GDImage\Exception\FileException;
use ShadeSoft\GDImage\Helper\ImageFile;
use ShadeSoft\GDImage\Helper\ImageOptions;

class ImageConverter
{

    /**
     * Convert image to given format
     * @param string $img
     * @param "jpeg"|"jpg"|"png"|"gif"|"bmp"|"webp" $outputFormat
     * @param string $targetPath
     * @param null|int $quality
     */
    public function convert($img, $outputFormat, $targetPath, $quality = null)
    {
        ImageFile::save(
            $targetPath,
            ImageFile::get($img),
            ImageFile::getType($img, $outputFormat),
            $quality
        );
    }
}
