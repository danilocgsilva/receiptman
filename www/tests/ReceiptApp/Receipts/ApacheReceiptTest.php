<?php

declare(strict_types=1);

namespace App\Tests\ReceiptApp\Receipts;

use App\Tests\Traits\GetSpecificFileTrait;
use PHPUnit\Framework\TestCase;
use App\ReceiptApp\Receipts\Apache;
use App\ReceiptApp\File;

class ApacheReceiptTest extends TestCase
{
    use GetSpecificFileTrait;

    private Apache $apacheReceipt;
    
    function setUp(): void
    {
        $this->apacheReceipt = new Apache();
    }

    public function testDockerFileContent(): void
    {
        $this->apacheReceipt->setName("apache_env");

        $dockerComposeFileContent = <<<EOF
        services:
          apache_env:
            image: 'httpd:latest'
            container_name: apache_env
        
        EOF;

        $dockerComposeFile = $this->apacheReceipt->getFiles();
        $dockerComposeFile = $this->getSpecificFile($dockerComposeFile, "docker-compose.yml");

        $this->assertInstanceOf(File::class, $dockerComposeFile);
        $this->assertSame($dockerComposeFileContent, $dockerComposeFile->content);
    }

    public function testDockerFileWithRedirection(): void
    {
        $this->apacheReceipt->setName("apache_redirect");
        $this->apacheReceipt->setHttpPortRedirection("80");

        $dockerComposeFileContent = <<<EOF
        services:
          apache_redirect:
            image: 'httpd:latest'
            container_name: apache_redirect
            ports:
              - '80:80'
        
        EOF;

        $dockerComposeFile = $this->apacheReceipt->getFiles();
        $dockerComposeFile = $this->getSpecificFile($dockerComposeFile, "docker-compose.yml");

        $this->assertInstanceOf(File::class, $dockerComposeFile);
        $this->assertSame($dockerComposeFileContent, $dockerComposeFile->content);
    }

    public function testDockerFileWwwRedirection(): void
    {
        $this->apacheReceipt->setName("apache_ewww");
        $this->apacheReceipt->onExposeWWW();

        $dockerComposeFileContent = <<<EOF
        services:
          apache_ewww:
            image: 'httpd:latest'
            container_name: apache_ewww
            volumes:
              - './html:/var/www/html'
        
        EOF;

        $dockerComposeFile = $this->apacheReceipt->getFiles();
        $dockerComposeFile = $this->getSpecificFile($dockerComposeFile, "docker-compose.yml");

        $this->assertInstanceOf(File::class, $dockerComposeFile);
        $this->assertSame($dockerComposeFileContent, $dockerComposeFile->content);
    }
}
