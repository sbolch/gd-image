<?php

use sbolch\GDImage\Converter;
use sbolch\GDImage\Exception\FileException;

require_once dirname(__DIR__) . '/Exception/FileException.php';
require_once dirname(__DIR__) . '/Exception/FileInvalidTypeException.php';
require_once dirname(__DIR__) . '/Exception/FileNotFoundException.php';
require_once dirname(__DIR__) . '/Helper/File.php';
require_once dirname(__DIR__) . '/Helper/Options.php';
require_once dirname(__DIR__) . '/Converter.php';

$args = getopt('i:o:f:q:', ['input:', 'output:', 'format:', 'quality:']);

$input = $args['i'] ?? $args['input'] ?? null;
$output = $args['o'] ?? $args['output'] ?? null;
$format = $args['f'] ?? $args['format'] ?? null;
$quality = $args['q'] ?? $args['quality'] ?? null;

if (!$input) {
    die('Missing input file.');
}

try {
    $i = (new Converter())->image($input);
} catch (FileException $ex) {
    die('Missing input file.');
}

if (!$output && !$format) {
    die('Missing output format.');
}

if ($output && !$format) {
    $xOutput = explode('.', $output);
    $format = end($xOutput);
}

$to = 'to' . ucfirst($format);

$i->$to();

if ($quality) {
    $i->quality($quality);
}

$i->target($output ?: "$input.$format")->save();
