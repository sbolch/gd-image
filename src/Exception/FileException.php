<?php

namespace ShadeSoft\GDImage\Exception;

use Exception;

class FileException extends Exception {
    public function __construct($message = "", $code = 0, $previous = null) {
        parent::__construct($message, $code, $previous);
    }

    public function __toString() {
        return __CLASS__.": [$this->code]: $this->message\n";
    }
}
