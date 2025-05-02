<?php

declare(strict_types=1);

namespace App\Tests\ReceiptApp\Receipts;

use PHPUnit\Framework\TestCase;
use App\ReceiptApp\Receipts\DotNet;
use App\Tests\Traits\GetSpecificFileTrait;
use App\Tests\Traits\MockFileSystemTrait;

class DotNetTest extends TestCase
{
    use GetSpecificFileTrait;
    use MockFileSystemTrait;

    private DotNet $receipt;

    function setUp(): void
    {
        $this->receipt = new DotNet(
            $this->getFileSystemMocked("", 0)
        );
    }

    public function testNextQuestions(): void
    {
        $questions = [];
        while ($question = $this->receipt->getNextQuestionPair()) {
            $questions[] = $question;
        }
        $this->assertCount(5, $questions);
    }

    public function testDockerComposeFileContent(): void
    {
        $this->receipt->setName("dotnet_env");
        $dockerComposeFile = $this->receipt->getFiles()[0];

        $expectedFileContent = <<<EOF
        services:
          dotnet_env:
            build:
              context: .
            container_name: dotnet_env

        EOF;

        $this->assertSame($expectedFileContent, $dockerComposeFile->content);
    }

    public function testSetHostMountVolume(): void
    {
        $this->receipt->setName("dotnet_env");
        $this->receipt->setHostMountVolume();

        $dockerComposeFile = $this->getSpecificFile($this->receipt->getFiles(), 'docker-compose.yml');

        $expectedFileContent = <<<EOF
        services:
          dotnet_env:
            build:
              context: .
            container_name: dotnet_env
            volumes:
              - './app:/app'

        EOF;

        $this->assertSame($expectedFileContent, $dockerComposeFile->content);
    }
}
