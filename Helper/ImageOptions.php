<?php

namespace ShadeSoft\GDImage\Helper;

class ImageOptions {

    /**
     * Copy transparency from source image to destination
     * @param resource $dstImg
     * @param resource $srcImg
     */
    public static function copyTransparency($dstImg, $srcImg) {
        $tIndex = imagecolortransparent($srcImg);
        $tColor = array('red' => 255, 'green' => 255, 'blue' => 255);
        if($tIndex >= 0) {
            $tColor = imagecolorsforindex($srcImg, $tIndex);
        }
        $tIndex = imagecolorallocate($dstImg, $tColor['red'], $tColor['green'], $tColor['blue']);
        imagefill($dstImg, 0, 0, $tIndex);
        imagecolortransparent($dstImg, $tIndex);
    }
}