<?php

use ShadeSoft\GDImage\Twig\ConverterExtension;
use ShadeSoft\GDImage\Twig\SizerExtension;
use Twig\Test\IntegrationTestCase;

class T3_TwigIntegrationTest extends IntegrationTestCase
{
    protected function getFixturesDir(): string
    {
        return __DIR__;
    }

    protected function getExtensions(): array|Generator
    {
        yield new ConverterExtension(dirname(__DIR__));
        yield new SizerExtension(dirname(__DIR__));
    }
}