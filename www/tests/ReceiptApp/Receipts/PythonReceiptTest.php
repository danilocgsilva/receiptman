<?php

declare(strict_types=1);

namespace App\Tests\ReceiptApp\Receipts;

use App\ReceiptApp\Receipts\PythonReceipt;
use App\ReceiptApp\Receipts\Questions\Types\QuestionEntry;
use PHPUnit\Framework\TestCase;
use App\Tests\Traits\GetSpecificFileTrait;
use App\Tests\Traits\MockFileSystemTrait;

class PythonReceiptTest extends TestCase
{
    use GetSpecificFileTrait;
    use MockFileSystemTrait;

    private PythonReceipt $pythonReceipt;

    function setUp(): void
    {
        $this->pythonReceipt = new PythonReceipt(
            $this->getFileSystemMocked("", 0)
        );
    }

    public function testTypeOfPropertiesQuestionPairs(): void
    {
        $questionsParis = $this->pythonReceipt->getPropertyQuestionsPairs();
        $this->assertInstanceOf(
            expected: QuestionEntry::class, 
            actual: $questionsParis[0]
        );
    }

    public function testGetDockerFile(): void
    {
        $expectedString = <<<EOF
        FROM debian:bookworm-slim

        RUN apt-get update
        RUN apt-get upgrade -y
        RUN apt-get install python3 -y
        
        CMD while : ; do sleep 1000; done
        EOF;

        $this->pythonReceipt
            ->setName("python_receipt");
        
        $receiptFiles = $this->pythonReceipt->getFiles();

        $dockerFile = $this->getSpecificFile($receiptFiles, "Dockerfile");

        $this->assertSame($expectedString, $dockerFile->content);
    }
}
