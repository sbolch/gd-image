<?php

namespace ShadeSoft\GDImage\Twig;

use ShadeSoft\GDImage\CachedSizer;
use ShadeSoft\GDImage\Exception\FileException;
use ShadeSoft\GDImage\Traits\ExtensionTrait;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class SizerExtension extends AbstractExtension
{
    use ExtensionTrait;

    private CachedSizer $sizer;
    private ?string $cacheDir;

    public function __construct(string $docroot = null, string $cacheDir = null)
    {
        $this->sizer = new CachedSizer();
        $this->docroot = $docroot ?: $_SERVER['DOCUMENT_ROOT'];
        $this->cacheDir = $cacheDir;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter(
                'widen',
                function (string $img, int $width, string $outputFormat = null, string $targetPath = null): string {
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
                    } catch (FileException) {
                        return $this->absolute($img);
                    }

                    return $targetPath ?: $img;
                }
            ),
            new TwigFilter(
                'heighten',
                function (string $img, int $height, string $outputFormat = null, string $targetPath = null): string {
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
                    } catch (FileException) {
                        return '';
                    }

                    return $targetPath ?: $img;
                }
            ),
            new TwigFilter(
                'maximize',
                function (string $img, int $width, int $height, string $outputFormat = null, string $targetPath = null): string {
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
                    } catch (FileException) {
                        return '';
                    }

                    return $targetPath ?: $img;
                }
            ),
            new TwigFilter(
                'thumbnail',
                function (string $img, int $width, int $height, string $outputFormat = null, string $targetPath = null): string {
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
                    } catch (FileException) {
                        return '';
                    }

                    return $targetPath ?: $img;
                }
            )
        ];
    }

    private function cacheFilename(string $img, string $appendix, string $format = null): string
    {
        $xImg = explode('.', $img);
        $count = count($xImg);

        $filename = '';
        for ($i = 0; $i < $count - 1; ++$i) {
            $filename .= $xImg[$i];
        }

        return $filename . "$appendix." . ($format ?: end($xImg));
    }

    public function getName(): string
    {
        return 'shadesoft_gd_sizer';
    }
}
