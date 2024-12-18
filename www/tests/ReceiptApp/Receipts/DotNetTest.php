<?php

declare(strict_types=1);

namespace App\Tests\ReceiptApp\Receipts;

use PHPUnit\Framework\TestCase;

class DotNetTest extends TestCase
{
    function setUp(): void
    {
        $this->apacheReceipt = new Apache();
    }
}
