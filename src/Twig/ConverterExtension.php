<?php

namespace ShadeSoft\GDImage\Twig;

use ShadeSoft\GDImage\Converter;
use ShadeSoft\GDImage\Exception\FileException;
use ShadeSoft\GDImage\Helper\File;
use Twig_Extension;
use Twig_SimpleFilter;

class ConverterExtension extends Twig_Extension
{
    private $converter;
    private $docroot;

    public function __construct($docroot = null)
    {
        $this->converter = new Converter();
        $this->docroot = $docroot ?: $_SERVER['DOCUMENT_ROOT'];
    }

    public function getFilters()
    {
        $filters = [];

        foreach (array_keys(File::FORMATS) as $format) {
            $filters[] = new Twig_SimpleFilter("convert_to_$format", function ($img, $targetPath = null, $quality = null) use ($format) {
                return $this->convert($img, $format, $targetPath ?: "$img.$format", $quality);
            });
        }

        return $filters;
    }

    private function convert($img, $format, $targetPath, $quality = null)
    {
        try {
            $to = 'to' . ucfirst($format);
            $ni = $this->converter->image($this->absolute($img))
                ->$to()
                ->target($this->absolute($targetPath));

            if ($quality) {
                $ni->quality($quality);
            }

            $ni->save();
        } catch (FileException $ex) {
            return '';
        }

        return $targetPath;
    }

    private function absolute($relative)
    {
        return str_replace('//', '/', "$this->docroot/$relative");
    }

    public function getName()
    {
        return 'shadesoft_gd_converter';
    }
}
