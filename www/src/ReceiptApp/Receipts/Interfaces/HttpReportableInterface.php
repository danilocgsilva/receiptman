<?php

declare(strict_types=1);

namespace App\ReceiptApp\Receipts\Interfaces;

interface HttpReportableInterface
{
    public function setHttpPortRedirection(string $httpPortRedirection): static;
}
