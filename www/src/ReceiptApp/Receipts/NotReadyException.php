<?php

declare(strict_types=1);

namespace App\ReceiptApp\Receipts;

use Exception;
use App\ReceiptApp\Receipts\Interfaces\ReceiptInterface;

class NotReadyException extends Exception
{
    private array $missingData = [];

    public function __construct(ReceiptInterface $receipt)
    {
        try {
            $receipt->getName();
        } catch (Exception $e) {
            $this->missingData[] = "name";
        }

        parent::__construct("The receipt still is not ready.");
    }
}
