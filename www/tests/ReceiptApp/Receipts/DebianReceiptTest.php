<?php

declare(strict_types=1);

namespace App\Tests\ReceiptApp\Receipts;

use App\ReceiptApp\Receipts\Debian;
use App\Tests\Traits\GetSpecificFileTrait;
use PHPUnit\Framework\TestCase;
class DebianReceiptTest extends TestCase
{
    use GetSpecificFileTrait;
    
    private Debian $debianReceipt;

    function setUp(): void
    {
        $this->debianReceipt = new Debian();
    }
}
