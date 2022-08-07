<?php

namespace ShadeSoft\GDImage;

use GdImage;
use ShadeSoft\GDImage\Exception\FileException;
use ShadeSoft\GDImage\Exception\FileInvalidTypeException;
use ShadeSoft\GDImage\Exception\MethodNotFoundException;
use ShadeSoft\GDImage\Helper\File;
use ShadeSoft\GDImage\Helper\Options;

/**
 * @method self toAvif()
 * @method self toBmp()
 * @method self toGif()
 * @method self toJpg()
 * @method self toJpeg()
 * @method self toPng()
 * @method self toWebp()
 */
class Converter
{
    protected GdImage $img;
    protected string $format;
    protected string $path;
    protected ?int $quality = null;
    protected ?array $background = null;
    protected string $originalFormat;

    /**
     * Resolve magic calls to set output format
     * @param array $args - First argument is quality percent as integer
     * @throws MethodNotFoundException|FileInvalidTypeException
     */
    public function __call(string $method, array $args): self
    {
        $availableFormats = File::FORMATS;

        if (!str_starts_with($method, 'to')) {
            throw new MethodNotFoundException("Method \"$method\" is not implemented.");
        }

        $format = lcfirst(substr($method, 2));

        if (!isset($availableFormats[$format])) {
            throw new FileInvalidTypeException("Not supported image type \"$format\".");
        }

        $this->format = $availableFormats[$format];

        if (isset($args[0]) && $args[0] != null) {
            $this->quality($args[0]);
        }

        return $this;
    }

    /**
     * Set output quality
     */
    public function quality(int $quality): self
    {
        if ($quality >= 0 && $quality <= 101) {
            $this->quality = $quality;
        }

        return $this;
    }

    /**
     * Set background color instead of trancparency
     */
    public function background(int $red, int $green, int $blue): self
    {
        $this->background = [
            'red'   => $red,
            'green' => $green,
            'blue'  => $blue,
        ];

        return $this;
    }

    /**
     * Set source image
     * @throws FileException
     */
    public function image(string|GdImage $image): self
    {
        $this->img = $image instanceof GdImage ? $image : File::get($image);

        if (gettype($image) == 'string') {
            $this->path = $image;
            $this->format = $this->originalFormat = File::getType($image);
        }

        return $this;
    }

    /**
     * Set target path
     */
    public function target(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Save generated image
     * @throws FileInvalidTypeException
     */
    public function save(): string
    {
        if (in_array($this->originalFormat, [File::AVIF, File::PNG, File::GIF, File::WEBP])) {
            if ($this->background) {
                Options::background($this->img, $this->background);
            } elseif (in_array($this->format, [File::AVIF, File::PNG, File::GIF, File::WEBP])) {
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
    public function output(): void
    {
        if (in_array($this->originalFormat, [File::AVIF, File::PNG, File::GIF, File::WEBP])) {
            if ($this->background) {
                Options::background($this->img, $this->background);
            } elseif (in_array($this->format, [File::AVIF, File::PNG, File::GIF, File::WEBP])) {
                Options::transparency($this->img, $this->format);
            }
        }

        File::printToOutput($this->img, $this->format, $this->quality);
    }
}
