<?php

namespace App;

use Exception;
use Throwable;

class NotReadyException extends Exception
{
    public function __construct()
    {
        parent::__construct(
            "The receipt is not ready."
        );
    }
}
