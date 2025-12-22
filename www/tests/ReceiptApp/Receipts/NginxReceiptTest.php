<?php

declare(strict_types=1);

namespace App\Tests\ReceiptApp\Receipts;

use App\Tests\Traits\GetSpecificFileTrait;
use PHPUnit\Framework\TestCase;
use App\ReceiptApp\Receipts\Questions\Types\QuestionEntry;
use App\ReceiptApp\Receipts\NginxReceipt;
use App\Tests\Traits\MockFileSystemTrait;

class NginxReceiptTest extends TestCase
{
    use GetSpecificFileTrait;
    use MockFileSystemTrait;

    private NginxReceipt $nginxReceipt;
    
    function setUp(): void
    {
        $this->nginxReceipt = new NginxReceipt(
            $this->getFileSystemMocked("", 0)
        );
    }

    public function testDockerComposeFileContent(): void
    {
        $this->nginxReceipt->setName("nginx_env");
//         $dockerComposeFile = $this->nginxReceipt->getFiles()[0];

//         $expectedFileContent = <<<EOF
// services:
//   nginx_env:
//     image: 'nginx:latest'
//     container_name: nginx_env

// EOF;

//         $this->assertSame($expectedFileContent, $dockerComposeFile->content);

        $yamlStructure = $this->nginxReceipt->getServiceYamlStructure();
        $this->assertIsArray($yamlStructure);
        $this->assertArrayHasKey("nginx_env", $yamlStructure);
        $this->assertArrayHasKey("image", $yamlStructure["nginx_env"]);
        $this->assertArrayHasKey("container_name", $yamlStructure["nginx_env"]);
    }

    public function testDockerComposeFilePort(): void
    {
        $this->nginxReceipt->setName("nginx_env");
        $this->nginxReceipt->setHttpPortRedirection("8081");

//         $dockerComposeFile = $this->nginxReceipt->getFiles()[0];

//         $expectedFileContent = <<<EOF
// services:
//   nginx_env:
//     image: 'nginx:latest'
//     container_name: nginx_env
//     ports:
//       - '8081:80'

// EOF;

//         $this->assertSame($expectedFileContent, $dockerComposeFile->content);

        $yamlStructure = $this->nginxReceipt->getServiceYamlStructure();
        $this->assertIsArray($yamlStructure);
        $this->assertArrayHasKey("nginx_env", $yamlStructure);
        $this->assertArrayHasKey("ports", $yamlStructure["nginx_env"]);
        $this->assertSame(['8081:80'], $yamlStructure["nginx_env"]["ports"]);
    }

    public function testCountReceiptExposingConfigurationFile(): void
    {
        $this->nginxReceipt->setName("nginx_env");
        $this->nginxReceipt->onExposeDefaultServerFile();

        $dockerComposeFile = $this->nginxReceipt->getFiles();

        $this->assertCount(2, $dockerComposeFile);
    }

    public function testGetPropertyQuestionsPairs(): void
    {
        $questionsParis = $this->nginxReceipt->getPropertyQuestionsPairs();
        $this->assertIsArray($questionsParis);
    }

    public function testTypeOfPropertiesQuestionPairs(): void
    {
        $questionsParis = $this->nginxReceipt->getPropertyQuestionsPairs();
        $this->assertInstanceOf(QuestionEntry::class, $questionsParis[0]);
    }

    public function testDockerFileContent(): void
    {
        $this->nginxReceipt->setName("nginx_env");

        $yamlStructure = $this->nginxReceipt->getServiceYamlStructure();

        $this->assertIsArray($yamlStructure);
        $this->assertArrayHasKey("nginx_env", $yamlStructure);
        $this->assertArrayHasKey("image", array: $yamlStructure["nginx_env"]);
        $this->assertArrayHasKey("container_name", $yamlStructure["nginx_env"]);
        $this->assertSame("nginx:latest", $yamlStructure["nginx_env"]["image"]);
        $this->assertSame("nginx_env", $yamlStructure["nginx_env"]["container_name"]);
    }
}
