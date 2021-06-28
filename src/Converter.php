<?php

namespace ShadeSoft\GDImage;

use ShadeSoft\GDImage\Exception\FileException;
use ShadeSoft\GDImage\Exception\FileInvalidTypeException;
use ShadeSoft\GDImage\Exception\MethodNotFoundException;
use ShadeSoft\GDImage\Helper\File;
use ShadeSoft\GDImage\Helper\Options;

class Converter {
    protected $img;
    protected $format;
    protected $path;
    protected $quality;
    protected $background;
    protected $originalFormat;

    /**
     * Resolve magic calls to set output format
     * @param string $method
     * @param array $args - First argument is quality percent as integer
     * @return self
     * @throws MethodNotFoundException|FileInvalidTypeException
     */
    public function __call($method, $args) {
        $availableFormats = File::FORMATS;

        if(strpos($method, 'to') !== 0) {
            throw new MethodNotFoundException("Method \"$method\" is not implemented.");
        }

        $format = lcfirst(substr($method, 2));

        if(!isset($availableFormats[$format])) {
            throw new FileInvalidTypeException("Not supported image type \"$format\".");
        }

        $this->format = $availableFormats[$format];

        if(isset($args[0]) && $args[0] != null) {
            $this->quality($args[0]);
        }

        return $this;
    }

    /**
     * Set output quality
     * @param int $quality
     * @return self
     */
    public function quality($quality) {
        if($quality >= 0 && $quality <= 100) {
            $this->quality = $quality;
        }

        return $this;
    }

    /**
     * Set background color instead of trancparency
     * @param int $red
     * @param int $green
     * @param int $blue
     * @return self
     */
    public function background($red, $green, $blue) {
        $this->background = [
            'red'   => $red,
            'green' => $green,
            'blue'  => $blue,
        ];

        return $this;
    }

    /**
     * Set source image
     * @param string|resource $image
     * @return self
     * @throws FileException
     */
    public function image($image) {
        $this->img = gettype($image) == 'resource' ? $image : File::get($image);

        if(gettype($image) == 'string') {
            $this->path = $image;
            $this->format = $this->originalFormat = File::getType($image);
        }

        return $this;
    }

    /**
     * Set target path
     * @param string $path
     * @return self
     */
    public function target($path) {
        $this->path = $path;

        return $this;
    }

    /**
     * Save generated image
     * @return string
     * @throws FileInvalidTypeException
     */
    public function save() {
        if(in_array($this->originalFormat, [File::PNG, File::GIF, File::WEBP])) {
            if($this->background) {
                Options::background($this->img, $this->background);
            } elseif(in_array($this->format, [File::PNG, File::GIF, File::WEBP])) {
                Options::transparency($this->img, $this->format);
            }
        }

        File::save($this->path, $this->img, $this->format, $this->quality);

        return $this->path;
    }

    /**
     * Print image to PHP output
     * @throws FileInvalidTypeException
     */
    public function output() {
        if(in_array($this->originalFormat, [File::PNG, File::GIF, File::WEBP])) {
            if($this->background) {
                Options::background($this->img, $this->background);
            } elseif(in_array($this->format, [File::PNG, File::GIF, File::WEBP])) {
                Options::transparency($this->img, $this->format);
            }
        }

        File::printToOutput($this->img, $this->format, $this->quality);
    }
}
