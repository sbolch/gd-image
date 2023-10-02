# GDImage

> An image editing package using PHP's GD library.

[![Latest Stable Version](https://poser.pugx.org/sbolch/gd-image/version)](https://packagist.org/packages/sbolch/gd-image)
[![StyleCI](https://styleci.io/repos/109691251/shield?branch=main&style=flat)](https://styleci.io/repos/109691251?branch=main)
[![Total Downloads](https://poser.pugx.org/sbolch/gd-image/downloads)](https://packagist.org/packages/sbolch/gd-image)
[![License](https://poser.pugx.org/sbolch/gd-image/license)](https://packagist.org/packages/sbolch/gd-image)

## Installation

Open a command console, enter your project directory and execute the following command to download the latest stable
version of this bundle:

```console
$ composer require sbolch/gd-image
```

This command requires you to have Composer installed globally, as explained in
the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

## Usage

### Image Converter

```php
<?php
  // ...
  use sbolch\GDImage\Converter;
  // ...
  class Demo {
    public function demo() {
      $img = 'path/to/image.png';

      $converter = new Converter();
      $converter
        ->image($img)
        ->toJpg()
        ->target('path/to/converted-image.jpg')
        ->save();
    }
  }
```

#### Available functions

- `image ( string | GdImage $image ) : self`

  Set source image from file path or image resource

- `target ( string $path ) : self`

  Set target path

- `quality ( int quality ) : self`

  Set output quality - accepted value is a percentage

- `background ( int red, int green, int blue ) : self`

  Set background color instead of transparency - accepted values are integers between 0 and 255

- `toAvif() | toBmp() | toGif() | toJpg() | toJpeg() | toPng() | toWebp() : self`

  Set output format

- `save() : string`

  Save generated image

- `output() : void`

  Print image to PHP output

### Image Sizer

```php
<?php
  // ...
  use sbolch\GDImage\Sizer;
  // ...
  class Demo {
    public function demo() {
      $img = 'path/to/image.jpg';

      $sizer = new Sizer();
      $sizer
        ->image($img)
        ->thumbnail(400, 300)
        ->save();
    }
  }
```

#### Available functions

**See available functions at Converter, all of them are available here, too**

- `image ( string | GdImage $image ) : self`

  Set source image from file path or image resource

- `instance(): GdImage`

  Return current image resource

- `widen ( int $width ) : self`

  Set the image to the given width while preserving its ratio

- `heighten ( int $height ) : self`

  Set the image to the given height while preserving its ratio

- `maximize ( int $width, int $height ) : self`

  Maximize image's size by its longer dimension while preserving its ratio

- `crop ( int $width, int $height [, int $x [, int $y]] ) : self`

  Crop picture to given dimensions starting at the given position

- `thumbnail ( int $width, int $height ) : self`

  Make a thumbnail by cropping the image by its shorter dimension (centered crop)

## Cached Image Sizer

Same as Image Sizer but it uses cache.

```php
<?php
  // ...
  use sbolch\GDImage\CachedSizer;
  // ...
  class Demo {
    public function demo() {
      $img = 'path/to/image.jpg';

      $sizer = new CachedSizer();
      $sizer
        ->image($img)
        ->thumbnail(400, 300)
        ->save();
    }
  }
```

## Twig integration

If you use Twig, you can include the extensions
- `sbolch\GDImage\Twig\ConverterExtension([string $docroot])`
- `sbolch\GDImage\Twig\SizerExtension([string $docroot [, string $cacheDir]])`

> You can override the default PHP document root with the optional $docroot parameter for both extensions.
> 
> You can use the cache mechanism in SizerExtension by using the optional $cacheDir parameter with your desired cache folder.  

Then you can use the below filters (question mark marks optional parameters)

```twig
{# Converting image and returning its new path via sbolch\GDImage\Twig\ConverterExtension #}
{{ 'path/to/image'|convert_to_avif(?targetPath, ?quality) }}
{{ 'path/to/image'|convert_to_bmp(?targetPath, ?quality) }}
{{ 'path/to/image'|convert_to_gif(?targetPath, ?quality) }}
{{ 'path/to/image'|convert_to_jpg(?targetPath, ?quality) }}
{{ 'path/to/image'|convert_to_jpeg(?targetPath, ?quality) }}
{{ 'path/to/image'|convert_to_png(?targetPath, ?quality) }}
{{ 'path/to/image'|convert_to_webp(?targetPath, ?quality) }}

{# Resizing image and returning its new path via sbolch\GDImage\Twig\SizerExtension #}
{{ 'path/to/image'|widen(width, ?outputFormat, ?targetPath) }}
{{ 'path/to/image'|heighten(height, ?outputFormat, ?targetPath) }}
{{ 'path/to/image'|maximize(width, height, ?outputFormat, ?targetPath) }}
{{ 'path/to/image'|thumbnail(width, height, ?outputFormat, ?targetPath) }}
```

## Phar mode (only Converter yet)

You can download the phar file on the [releases](https://github.com/sbolch/GDImage/releases) page and use it as below:

```sh
php converter.phar -i /path/to/image -f jpg
```

- -i (--input) : Input file
- -o (--output) : Output file (optional - you must use -o or -f)
- -f (--format) : Output format (optional - you must use -o or -f)
- -q (--quality): Encoding quality as percentage (optional)
