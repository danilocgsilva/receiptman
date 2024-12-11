<?php

declare(strict_types=1);

namespace App\Tests\ReceiptApp\Receipts;

use App\ReceiptApp\Receipts\PythonReceipt;
use App\ReceiptApp\Receipts\Questions\QuestionEntry;
use PHPUnit\Framework\TestCase;
use App\Tests\Traits\GetSpecificFileTrait;

class PythonReceiptTest extends TestCase
{
    use GetSpecificFileTrait;

    private PythonReceipt $pythonReceipt;

    function setUp(): void
    {
        $this->pythonReceipt = new PythonReceipt();
    }

    public function testTypeOfPropertiesQuestionPairs(): void
    {
        $questionsParis = $this->pythonReceipt->getPropertyQuestionsPairs();
        $this->assertInstanceOf(
            expected: QuestionEntry::class, 
            actual: $questionsParis[0]
        );
    }
}
