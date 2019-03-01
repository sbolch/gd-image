<?php

namespace ShadeSoft\GDImage\Helper;

class ImageCache
{
    private $enabled;

    public function __construct()
    {
        $this->disable();
    }

    /**
     * Enable image cache
     */
    public function enable()
    {
        $this->enabled = true;
    }

    /**
     * Disable image cache
     */
    public function disable()
    {
        $this->enabled = false;
    }

    /**
     * Check if path is cached
     * @param $path
     * @return bool
     */
    public function cached($path)
    {
        return $this->enabled && file_exists($path);
    }
}
