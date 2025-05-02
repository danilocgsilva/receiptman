<?php

declare(strict_types=1);

namespace App\Tests\ReceiptApp\Receipts;

use PHPUnit\Framework\TestCase;
use App\ReceiptApp\Receipts\MySQLReceipt;
use App\Tests\Traits\GetSpecificFileTrait;
use App\Tests\Traits\MockFileSystemTrait;
use App\ReceiptApp\File;

class MySQLReceiptTest extends TestCase
{
    use GetSpecificFileTrait;
    use MockFileSystemTrait;

    private MySQLReceipt $mySQLReceipt;

    function setUp(): void
    {
        $this->mySQLReceipt = new MySQLReceipt(
            $this->getFileSystemMocked("", 0)
        );
    }

    public function testDockerFileContent(): void
    {
        $this->mySQLReceipt->setName("mysql_env");
        $this->mySQLReceipt->setMysqlPortRedirection("3306");
        $this->mySQLReceipt->setMysqlRootPassword("mysecretpass");

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

        $dockerComposeFile = $this->mySQLReceipt->getFiles();
        $dockerComposeFile = $this->getSpecificFile($dockerComposeFile, "docker-compose.yml");

        $this->assertInstanceOf(File::class, $dockerComposeFile);
        $this->assertSame($dockerComposeFileContent, $dockerComposeFile->content);
    }

    public function testDockerFileContentDifferentPortdAndPassword(): void
    {
        $this->mySQLReceipt->setName("mysql_env");
        $this->mySQLReceipt->setMysqlPortRedirection("7162");
        $this->mySQLReceipt->setMysqlRootPassword("anotherveryhardsecret");

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

        $dockerComposeFile = $this->mySQLReceipt->getFiles();
        $dockerComposeFile = $this->getSpecificFile($dockerComposeFile, "docker-compose.yml");

        $this->assertInstanceOf(File::class, $dockerComposeFile);
        $this->assertSame($dockerComposeFileContent, $dockerComposeFile->content);
    }
}
