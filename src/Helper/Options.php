<?php

namespace ShadeSoft\GDImage\Helper;

class Options
{

    /**
     * Copy transparency from source image
     * @param resource $to
     * @param resource $from
     */
    public static function transparency($to, $from)
    {
        $tIndex = imagecolortransparent($from);
        $tColor = array('red' => 255, 'green' => 255, 'blue' => 255);
        if ($tIndex >= 0) {
            $tColor = imagecolorsforindex($from, $tIndex);
        }
        $tIndex = imagecolorallocate($to, $tColor['red'], $tColor['green'], $tColor['blue']);
        imagefill($to, 0, 0, $tIndex);
        imagecolortransparent($to, $tIndex);
    }
}
