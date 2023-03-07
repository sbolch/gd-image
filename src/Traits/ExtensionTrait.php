<?php

namespace ShadeSoft\GDImage\Traits;

trait ExtensionTrait
{
    private string $docroot;

    private function absolute(string $relative): string
    {
        return str_replace('//', '/', "$this->docroot/$relative");
    }
}
