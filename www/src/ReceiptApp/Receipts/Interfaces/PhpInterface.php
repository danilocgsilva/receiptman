<?php

declare(strict_types=1);

namespace App\ReceiptApp\Receipts\Interfaces;

interface PhpInterface
{
    public function setPhpVersion(string $phpVersion): static;
}
