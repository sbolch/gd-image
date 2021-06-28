<?php

namespace ShadeSoft\GDImage;

class CachedSizer extends Sizer
{

    /**
     * @see Sizer::widen()
     */
    public function widen($width)
    {
        if ($this->path && file_exists($this->path)) {
            return $this;
        }

        return parent::widen($width);
    }

    /**
     * @see Sizer::heighten()
     */
    public function heighten($height)
    {
        if ($this->path && file_exists($this->path)) {
            return $this;
        }

        return parent::heighten($height);
    }

    /**
     * @see Sizer::maximize()
     */
    public function maximize($width, $height)
    {
        if ($this->path && file_exists($this->path)) {
            return $this;
        }

        return parent::maximize($width, $height);
    }

    /**
     * @see Sizer::thumbnail()
     */
    public function thumbnail($width, $height)
    {
        if ($this->path && file_exists($this->path)) {
            return $this;
        }

        return parent::thumbnail($width, $height);
    }
}
