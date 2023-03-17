<?php

namespace ShadeSoft\GDImage\Twig;

use ShadeSoft\GDImage\CachedSizer;
use ShadeSoft\GDImage\Exception\FileException;
use Twig_Extension;
use Twig_Filter;

class SizerExtension extends Twig_Extension
{
    private $sizer;
    private $docroot;
    private $cacheDir;

    public function __construct($docroot = null, $cacheDir = null)
    {
        $this->sizer = new CachedSizer();
        $this->docroot = $docroot ?: $_SERVER['DOCUMENT_ROOT'];
        $this->cacheDir = $cacheDir;
    }

    public function getFilters()
    {
        return [
            new Twig_Filter(
                'widen',
                function ($img, $width, $outputFormat = null, $targetPath = null) {
                    if ($targetPath && $this->cacheDir) {
                        $targetPath = $this->cacheDir . $this->cacheFilename($img, "_w$width", $outputFormat);

                        if (file_exists($this->absolute($targetPath))) {
                            return $targetPath;
                        }
                    }

                    try {
                        $ni = $this->sizer->image($this->absolute($img));

                        if ($outputFormat) {
                            $to = 'to' . ucfirst($outputFormat);
                            $ni->$to();
                        }

                        if ($targetPath) {
                            $ni->target($this->absolute($targetPath));
                        }

                        $ni->widen($width)->save();
                    } catch (FileException $ex) {
                        return $this->absolute($img);
                    }

                    return $targetPath ?: $img;
                }
            ),
            new Twig_Filter(
                'heighten',
                function ($img, $height, $outputFormat = null, $targetPath = null) {
                    if ($targetPath && $this->cacheDir) {
                        $targetPath = $this->cacheDir . $this->cacheFilename($img, "_h$height", $outputFormat);

                        if (file_exists($this->absolute($targetPath))) {
                            return $targetPath;
                        }
                    }

                    try {
                        $ni = $this->sizer->image($this->absolute($img));

                        if ($outputFormat) {
                            $to = 'to' . ucfirst($outputFormat);
                            $ni->$to();
                        }

                        if ($targetPath) {
                            $ni->target($this->absolute($targetPath));
                        }

                        $ni->heighten($height)->save();
                    } catch (FileException $ex) {
                        return '';
                    }

                    return $targetPath ?: $img;
                }
            ),
            new Twig_Filter(
                'maximize',
                function ($img, $width, $height, $outputFormat = null, $targetPath = null) {
                    if ($targetPath && $this->cacheDir) {
                        $targetPath = $this->cacheDir . $this->cacheFilename($img, "_m($width)_$height", $outputFormat);

                        if (file_exists($this->absolute($targetPath))) {
                            return $targetPath;
                        }
                    }

                    try {
                        $ni = $this->sizer->image($this->absolute($img));

                        if ($outputFormat) {
                            $to = 'to' . ucfirst($outputFormat);
                            $ni->$to();
                        }

                        if ($targetPath) {
                            $ni->target($this->absolute($targetPath));
                        }

                        $ni->maximize($width, $height)->save();
                    } catch (FileException $ex) {
                        return '';
                    }

                    return $targetPath ?: $img;
                }
            ),
            new Twig_Filter(
                'thumbnail',
                function ($img, $width, $height, $outputFormat = null, $targetPath = null) {
                    if ($targetPath && $this->cacheDir) {
                        $targetPath = $this->cacheDir . $this->cacheFilename($img, "_thumb($width)_$height", $outputFormat);

                        if (file_exists($this->absolute($targetPath))) {
                            return $targetPath;
                        }
                    }

                    try {
                        $ni = $this->sizer->image($this->absolute($img));

                        if ($outputFormat) {
                            $to = 'to' . ucfirst($outputFormat);
                            $ni->$to();
                        }

                        if ($targetPath) {
                            $ni->target($this->absolute($targetPath));
                        }

                        $ni->thumbnail($width, $height)->save();
                    } catch (FileException $ex) {
                        return '';
                    }

                    return $targetPath ?: $img;
                }
            )
        ];
    }

    private function cacheFilename($img, $appendix, $format = null)
    {
        $xImg = explode('.', $img);
        $count = count($xImg);

        $filename = '';
        for ($i = 0; $i < $count - 1; ++$i) {
            $filename .= $xImg[$i];
        }

        return $filename . "$appendix." . ($format ?: end($xImg));
    }

    private function absolute($relative)
    {
        return str_replace('//', '/', "$this->docroot/$relative");
    }

    public function getName()
    {
        return 'shadesoft_gd_sizer';
    }
}
