<?php

declare(strict_types=1);

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use App\ReceiptApp\Receipts\ApacheReceipt;
use App\ReceiptApp\Receipts\DotNet;
use App\Tests\Traits\MockFileSystemTrait;
use App\Utilities\WrapServicesYamlStructure;
use Symfony\Component\Yaml\Yaml;
use App\ReceiptApp\Receipts\Interfaces\ReceiptInterface;

class WrapServicesYamlStructureTest extends TestCase
{
    use MockFileSystemTrait;

    public function testDockerFileContentForApacheReceipt(): void
    {
        $apacheReceipt = $this->getApacheReceipt();
        $apacheReceipt->setName("apache_env");

        $dockerComposeFileContent = <<<EOF
        services:
          apache_env:
            image: 'httpd:latest'
            container_name: apache_env
        
        EOF;

        $this->testApacheReceiptInstance($apacheReceipt, $dockerComposeFileContent);
    }

    public function testDockerFileContentForApacheReceiptWithRedirection(): void
    {
        $apacheReceipt = $this->getApacheReceipt();
        $apacheReceipt->setName("apache_env_redirect");
        $apacheReceipt->setHttpPortRedirection("80");

        $dockerComposeFileContent = <<<EOF
        services:
          apache_env_redirect:
            image: 'httpd:latest'
            container_name: apache_env_redirect
            ports:
              - '80:80'
        
        EOF;

        $this->testApacheReceiptInstance($apacheReceipt, $dockerComposeFileContent);
    }

    public function testDockerFileContentForApacheReceiptWithWWW()
    {
        $apacheReceipt = $this->getApacheReceipt();
        $apacheReceipt->setName("apache_www");
        $apacheReceipt->onExposeWWW();

        $dockerComposeFileContent = <<<EOF
        services:
          apache_www:
            image: 'httpd:latest'
            container_name: apache_www
            volumes:
              - './html:/var/www/html'
        
        EOF;

        $this->testApacheReceiptInstance($apacheReceipt, $dockerComposeFileContent);
    }

    public function testDockerFileContentForApacheReceiptWithRedirectionAndWWW()
    {
        $apacheReceipt = $this->getApacheReceipt();
        $apacheReceipt->setName("apache_www_redirect");
        $apacheReceipt->setHttpPortRedirection("8080");
        $apacheReceipt->onExposeWWW();

        $dockerComposeFileContent = <<<EOF
        services:
          apache_www_redirect:
            image: 'httpd:latest'
            container_name: apache_www_redirect
            ports:
              - '8080:80'
            volumes:
              - './html:/var/www/html'
        
        EOF;

        $this->testApacheReceiptInstance($apacheReceipt, $dockerComposeFileContent);
    }

    public function testDockerFileContentForApacheReceiptWithHostMode()
    {
        $apacheReceipt = $this->getApacheReceipt();
        $apacheReceipt->setName("apache_ewww");
        $apacheReceipt->setNetworkModeHost();

        $dockerComposeFileContent = <<<EOF
        services:
          apache_ewww:
            image: 'httpd:latest'
            container_name: apache_ewww
            network_mode: host
        
        EOF;

        $this->testApacheReceiptInstance($apacheReceipt, $dockerComposeFileContent);
    }

    private function getApacheReceipt(): ApacheReceipt
    {
        return new ApacheReceipt($this->getFileSystemMocked("", 0));
    }

    private function getDotNetReceipt(): ReceiptInterface
    {
        return new ApacheReceipt($this->getFileSystemMocked("", 0));
    }

    private function testApacheReceiptInstance(ReceiptInterface $receipt, string $dockerComposeFileContent): void
    {
        $wrapServicesYamlStructure = new WrapServicesYamlStructure($receipt);
        $yamlFullStructure = $wrapServicesYamlStructure->getFullDockerComposeYamlStructure();

        $fileContent = Yaml::dump($yamlFullStructure, 4, 2);

        $this->assertSame($dockerComposeFileContent, $fileContent);
    }
}
