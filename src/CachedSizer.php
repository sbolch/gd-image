<?php

namespace sbolch\GDImage;

class CachedSizer extends Sizer
{
    /**
     * @see Sizer::widen()
     */
    public function widen(int $width): self
    {
        if ($this->path && file_exists($this->path)) {
            return $this;
        }

        return parent::widen($width);
    }

    /**
     * @see Sizer::heighten()
     */
    public function heighten(int $height): self
    {
        if ($this->path && file_exists($this->path)) {
            return $this;
        }

        return parent::heighten($height);
    }

    /**
     * @see Sizer::maximize()
     */
    public function maximize(int $width, int $height): self
    {
        if ($this->path && file_exists($this->path)) {
            return $this;
        }

        return parent::maximize($width, $height);
    }

    /**
     * @see Sizer::thumbnail()
     */
    public function thumbnail(int $width, int $height): self
    {
        if ($this->path && file_exists($this->path)) {
            return $this;
        }

        return parent::thumbnail($width, $height);
    }
}
