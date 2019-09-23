<?php

namespace ShadeSoft\GDImage\Helper;

class Options
{

    /**
     * @param resource $to
     * @param string $format
     */
    public static function transparency($img, $format)
    {
        if ($format == File::GIF) {
            $transparent = imagecolorallocatealpha($img, 0, 0, 0, 127);
            imagefill($img, 0, 0, $transparent);
            imagecolortransparent($img, $transparent);
        } else {
            imagealphablending($img, false);
        }

        imagesavealpha($img, true);
    }
}
