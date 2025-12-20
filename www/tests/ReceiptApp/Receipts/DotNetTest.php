<?php

declare(strict_types=1);

namespace App\Tests\ReceiptApp\Receipts;

use PHPUnit\Framework\TestCase;
use App\ReceiptApp\Receipts\DotNetReceipt;
use App\Tests\Traits\GetSpecificFileTrait;
use App\Tests\Traits\MockFileSystemTrait;

class DotNetTest extends TestCase
{
    use GetSpecificFileTrait;
    use MockFileSystemTrait;

    private DotNetReceipt $receipt;

    function setUp(): void
    {
        $this->receipt = new DotNetReceipt(
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

        $yamlStructure = $this->receipt->getServiceYamlStructure();

        $this->assertIsArray($yamlStructure);
        $this->assertArrayHasKey("dotnet_env", $yamlStructure);
        $this->assertArrayHasKey("build", $yamlStructure["dotnet_env"]);
        $this->assertArrayHasKey("container_name", $yamlStructure["dotnet_env"]);
    }

    public function testSetHostMountVolume(): void
    {
        $this->receipt->setName("dotnet_env");
        $this->receipt->setHostMountVolume();

        $yamlStructure = $this->receipt->getServiceYamlStructure();

        $this->assertIsArray($yamlStructure);
        $this->assertArrayHasKey("dotnet_env", $yamlStructure);
        $this->assertArrayHasKey("build", $yamlStructure["dotnet_env"]);
        $this->assertArrayHasKey("container_name", $yamlStructure["dotnet_env"]);
        $this->assertArrayHasKey("volumes", $yamlStructure["dotnet_env"]);
        $this->assertSame("./app:/app", $yamlStructure["dotnet_env"]["volumes"][0]);
    }
}
