<?php

namespace sbolch\GDImage\Twig;

use sbolch\GDImage\Converter;
use sbolch\GDImage\Exception\FileException;
use sbolch\GDImage\Helper\File;
use sbolch\GDImage\Traits\ExtensionTrait;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class ConverterExtension extends AbstractExtension
{
    use ExtensionTrait;

    private Converter $converter;

    public function __construct(string $docroot = null)
    {
        $this->converter = new Converter();
        $this->docroot = $docroot ?: $_SERVER['DOCUMENT_ROOT'];
    }

    public function getFilters(): array
    {
        $filters = [];

        foreach (array_keys(File::FORMATS) as $format) {
            $filters[] = new TwigFilter("convert_to_$format", function (string $img, string $targetPath = null, int $quality = null) use ($format) {
                return $this->convert($img, $format, $targetPath ?: "$img.$format", $quality);
            });
        }

        return $filters;
    }

    private function convert(string $img, string $format, string $targetPath, int $quality = null): string
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
        } catch (FileException) {
            return '';
        }

        return $targetPath;
    }

    public function getName(): string
    {
        return 'sbolch_gd_converter';
    }
}
