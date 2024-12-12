<?php

declare(strict_types=1);

namespace App\Tests\ReceiptApp\Receipts;

use App\Tests\Traits\GetSpecificFileTrait;
use PHPUnit\Framework\TestCase;
use App\ReceiptApp\Receipts\Questions\QuestionEntry;
use App\ReceiptApp\Receipts\NginxReceipt;
use App\ReceiptApp\File;

class NginxReceiptTest extends TestCase
{
    use GetSpecificFileTrait;

    private NginxReceipt $nginxReceipt;
    
    function setUp(): void
    {
        $this->nginxReceipt = new NginxReceipt();
    }

    public function testDockerComposeFileContent(): void
    {
        $this->nginxReceipt->setName("nginx_env");
        $dockerComposeFile = $this->nginxReceipt->getFiles()[0];

        $expectedFileContent = <<<EOF
services:
  nginx_env:
    image: 'nginx:latest'
    container_name: nginx_env

EOF;

        $this->assertSame($expectedFileContent, $dockerComposeFile->content);
    }

    public function testDockerComposeFilePort(): void
    {
        $this->nginxReceipt->setName("nginx_env");
        $this->nginxReceipt->setHttpPortRedirection("8081");

        $dockerComposeFile = $this->nginxReceipt->getFiles()[0];

        $expectedFileContent = <<<EOF
services:
  nginx_env:
    image: 'nginx:latest'
    container_name: nginx_env
    ports:
      - '8081:80'

EOF;

        $this->assertSame($expectedFileContent, $dockerComposeFile->content);
    }

    public function testCountReceiptFiles(): void
    {
        $this->nginxReceipt->setName("nginx_env");

        $dockerComposeFile = $this->nginxReceipt->getFiles();

        $this->assertCount(1, $dockerComposeFile);
    }

    public function testCountReceiptExposingConfigurationFile(): void
    {
        $this->nginxReceipt->setName("nginx_env");
        $this->nginxReceipt->onExposeDefaultServerFile();

        $dockerComposeFile = $this->nginxReceipt->getFiles();

        $this->assertCount(3, $dockerComposeFile);
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

        $dockerComposeFileContent = <<<EOF
        services:
          nginx_env:
            image: 'nginx:latest'
            container_name: nginx_env
        
        EOF;

        $dockerComposeFile = $this->nginxReceipt->getFiles();
        $dockerComposeFile = $this->getSpecificFile($dockerComposeFile, "docker-compose.yml");

        $this->assertInstanceOf(File::class, $dockerComposeFile);
        $this->assertSame($dockerComposeFileContent, $dockerComposeFile->content);
    }
}
