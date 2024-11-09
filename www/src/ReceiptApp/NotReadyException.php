<?php

declare(strict_types=1);

namespace App;

use Exception;

class NotReadyException extends Exception
{
    public function __construct()
    {
        parent::__construct(
            "The receipt is not ready."
        );
    }
}
