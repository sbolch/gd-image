<?php

namespace ShadeSoft\GDImage\Twig;

use ShadeSoft\GDImage\Converter;
use ShadeSoft\GDImage\Exception\FileException;
use ShadeSoft\GDImage\Exception\MethodNotFoundException;
use ShadeSoft\GDImage\Helper\File;
use Twig_Extension;
use Twig_SimpleFilter;

class ConverterExtension extends Twig_Extension
{
    private $converter;
    private $docroot;

    public function __construct(Converter $converter)
    {
        $this->converter = $converter;
        $this->docroot = $_SERVER['DOCUMENT_ROOT'];
    }

    public function getFilters()
    {
        $filters = [];

        foreach (array_keys(File::FORMATS) as $format) {
            $filters[] = new Twig_SimpleFilter("convert_to_$format", [$this, 'to'.ucfirst($format)]);
        }

        return $filters;
    }

    /**
     * Resolve magic calls to convert image
     * @param string $method
     * @param array $args
     * - First argument is source file path or image resource
     * - Second argument is target path
     * - Third argument is quality percent as integer
     * @return string
     * @throws MethodNotFoundException|FileException
     */
    public function __call($method, $args)
    {
        if (strpos($method, 'to') === 0) {
            throw new MethodNotFoundException("");
        }

        return $this->converter
            ->image($this->absPath($args[0]))
            ->target(isset($args[1]) ? $args[1] : $args[0])
            ->$method(
                [isset($args[2]) ? $args[2] : null]
            )
            ->save();
    }

    private function absPath($path)
    {
        return $this->docroot.$path;
    }

    public function getName()
    {
        return 'shadesoft_gd_converter';
    }
}
