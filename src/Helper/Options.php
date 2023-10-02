<?php

namespace sbolch\GDImage\Helper;

use GdImage;

class Options
{
    public static function transparency(GdImage $img, int $format): void
    {
        if ($format == IMAGETYPE_GIF) {
            $transparent = imagecolorallocatealpha($img, 0, 0, 0, 127);
            imagefill($img, 0, 0, $transparent);
            imagecolortransparent($img, $transparent);
        } else {
            imagealphablending($img, false);
        }

        imagesavealpha($img, true);
    }

    /**
     * @param int[] $color
     */
    public static function background(GdImage $img, array $color): void
    {
        imagefill($img, 0, 0, imagecolorallocate($img, $color['red'], $color['green'], $color['blue']));
    }
}
