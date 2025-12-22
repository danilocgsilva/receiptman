<?php

declare(strict_types=1);

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use App\ReceiptApp\Receipts\ApacheReceipt;
use App\ReceiptApp\Receipts\DotNetReceipt;
use App\ReceiptApp\Receipts\MySQLReceipt;
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

    public function testDockerComposeFileContentForDotNetReceipt(): void
    {
        $dotnetReceipt = $this->getDotNetReceipt();
        $dotnetReceipt->setName("dotnet_env");

        $dockerComposeFileContent = <<<EOF
        services:
          dotnet_env:
            build:
              context: .
            container_name: dotnet_env
        
        EOF;

        $this->testDotNetReceiptInstance($dotnetReceipt, $dockerComposeFileContent);
    }

    public function testDockerComposeFileContentForDotNetSetHostMountVolume(): void
    {
        $dotnetReceipt = $this->getDotNetReceipt();
        $dotnetReceipt->setName("dotnet_env");
        $dotnetReceipt->setHostMountVolume();

        $dockerComposeFileContent = <<<EOF
        services:
          dotnet_env:
            build:
              context: .
            container_name: dotnet_env
            volumes:
              - './app:/app'
        
        EOF;

        $this->testDotNetReceiptInstance($dotnetReceipt, $dockerComposeFileContent);
    }

    public function testDockerFileContentForMySQLReceipt(): void
    {
        $mySQLReceipt = $this->getMySQLReceipt();
        $mySQLReceipt->setName("mysql_env");
        $mySQLReceipt->setMysqlPortRedirection("3306");
        $mySQLReceipt->setMysqlRootPassword("mysecretpass");

        $dockerComposeFileContent = <<<EOF
        services:
          mysql_env:
            image: 'mysql:latest'
            container_name: mysql_env
            environment:
              - MYSQL_ROOT_PASSWORD=mysecretpass
            ports:
              - '3306:3306'
        
        EOF;

        $this->testMysqLReceiptInstance($mySQLReceipt, $dockerComposeFileContent);
    }

    public function testDockerFileContenFortDifferentPortdAndPassword()
    {
        $mySQLReceipt = $this->getMySQLReceipt();
        $mySQLReceipt->setName("mysql_env");
        $mySQLReceipt->setMysqlPortRedirection("7162");
        $mySQLReceipt->setMysqlRootPassword("anotherveryhardsecret");

        $dockerComposeFileContent = <<<EOF
        services:
          mysql_env:
            image: 'mysql:latest'
            container_name: mysql_env
            environment:
              - MYSQL_ROOT_PASSWORD=anotherveryhardsecret
            ports:
              - '7162:3306'

        EOF;

        $this->testMysqLReceiptInstance($mySQLReceipt, $dockerComposeFileContent);
    }

    public function testMergeTwoRecipes()
    {
        $apacheReceipt = $this->getApacheReceipt();
        $apacheReceipt->setName("apache_env");

        $mySQLReceipt = $this->getMySQLReceipt();
        $mySQLReceipt->setName("mysql_env");
        $mySQLReceipt->setMysqlPortRedirection("3306");
        $mySQLReceipt->setMysqlRootPassword("mysecretpass");

        $wrapServicesYamlStructure = new WrapServicesYamlStructure($apacheReceipt, $mySQLReceipt);
        $yamlFullStructure = $wrapServicesYamlStructure->getFullDockerComposeYamlStructure();

        $fileContent = Yaml::dump($yamlFullStructure, 4, 2);

        $dockerComposeFileContent = <<<EOF
        services:
          apache_env:
            image: 'httpd:latest'
            container_name: apache_env
          mysql_env:
            image: 'mysql:latest'
            container_name: mysql_env
            environment:
              - MYSQL_ROOT_PASSWORD=mysecretpass
            ports:
              - '3306:3306'
        
        EOF;

        $this->assertSame($dockerComposeFileContent, $fileContent);
    }

    private function getApacheReceipt(): ApacheReceipt
    {
        return new ApacheReceipt($this->getFileSystemMocked("", 0));
    }

    private function getDotNetReceipt(): DotNetReceipt
    {
        return new DotNetReceipt($this->getFileSystemMocked("", 0));
    }

    private function getMySQLReceipt(): MySQLReceipt
    {
        return new MySQLReceipt($this->getFileSystemMocked("", 0));
    }

    private function testMysqLReceiptInstance(MySQLReceipt $receipt, string $dockerComposeFileContent): void
    {
        $wrapServicesYamlStructure = new WrapServicesYamlStructure($receipt);
        $yamlFullStructure = $wrapServicesYamlStructure->getFullDockerComposeYamlStructure();

        $fileContent = Yaml::dump($yamlFullStructure, 4, 2);

        $this->assertSame($dockerComposeFileContent, $fileContent);
    }

    private function testDotNetReceiptInstance(ReceiptInterface $receipt, string $dockerComposeFileContent): void
    {
        $wrapServicesYamlStructure = new WrapServicesYamlStructure($receipt);
        $yamlFullStructure = $wrapServicesYamlStructure->getFullDockerComposeYamlStructure();

        $fileContent = Yaml::dump($yamlFullStructure, 4, 2);

        $this->assertSame($dockerComposeFileContent, $fileContent);
    }

    private function testApacheReceiptInstance(ReceiptInterface $receipt, string $dockerComposeFileContent): void
    {
        $wrapServicesYamlStructure = new WrapServicesYamlStructure($receipt);
        $yamlFullStructure = $wrapServicesYamlStructure->getFullDockerComposeYamlStructure();

        $fileContent = Yaml::dump($yamlFullStructure, 4, 2);

        $this->assertSame($dockerComposeFileContent, $fileContent);
    }
}
