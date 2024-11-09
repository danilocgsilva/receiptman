<?php

declare(strict_types=1);

namespace App\ReceiptApp\Receipts;

use Exception;

class NotReadyException extends Exception
{
    public function __construct()
    {
        $message = "The receipt still is not ready. Plase, set a name for it.";
        
        parent::__construct($message);
    }
}
