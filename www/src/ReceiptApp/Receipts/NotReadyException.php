<?php

declare(strict_types=1);

namespace App\ReceiptApp\Receipts;

use Exception;
use Error;
use App\ReceiptApp\Receipts\Interfaces\ReceiptInterface;

class NotReadyException extends Exception
{
    private array $missingData = [];

    public function __construct(ReceiptInterface $receipt)
    {
        try {
            $receipt->getName();
        } catch (Error $e) {
            $this->missingData[] = "name";
        }

        try {
            $receipt->getHttpPortRedirection();
        } catch (Error $e) {
            $this->missingData[] = "httpPortRedirection";
        }

        parent::__construct("The receipt still is not ready. Missing data: " . implode(", ", $this->missingData) . ".");
    }
}
