<?php

declare(strict_types=1);

namespace App\Tests\ReceiptApp\Receipts;

use PHPUnit\Framework\TestCase;
use App\ReceiptApp\Receipts\MySQLReceipt;
use App\Tests\Traits\GetSpecificFileTrait;
use App\Tests\Traits\MockFileSystemTrait;

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

        $yamlStructure = $this->mySQLReceipt->getServiceYamlStructure();

        $this->assertArrayHasKey('mysql_env', $yamlStructure);
        $this->assertArrayHasKey('image', $yamlStructure["mysql_env"]);
        $this->assertArrayHasKey('container_name', $yamlStructure["mysql_env"]);
        $this->assertArrayHasKey('environment', $yamlStructure["mysql_env"]);
        $this->assertArrayHasKey('ports', $yamlStructure["mysql_env"]);
        $this->assertEquals('mysql:latest', $yamlStructure["mysql_env"]['image']);
        $this->assertEquals('mysql_env', $yamlStructure["mysql_env"]['container_name']);
        $this->assertEquals('MYSQL_ROOT_PASSWORD=mysecretpass', $yamlStructure["mysql_env"]['environment'][0]);
        $this->assertEquals('3306:3306', $yamlStructure["mysql_env"]['ports'][0]);
    }

    public function testDockerFileContentDifferentPortdAndPassword(): void
    {
        $this->mySQLReceipt->setName("mysql_env");
        $this->mySQLReceipt->setMysqlPortRedirection("7162");
        $this->mySQLReceipt->setMysqlRootPassword("anotherveryhardsecret");

        $yamlStructure = $this->mySQLReceipt->getServiceYamlStructure();
        $this->assertArrayHasKey('mysql_env', $yamlStructure);
        $this->assertArrayHasKey('image', $yamlStructure['mysql_env']);
        $this->assertArrayHasKey('container_name', $yamlStructure['mysql_env']);
        $this->assertArrayHasKey('environment', $yamlStructure['mysql_env']);
        $this->assertArrayHasKey('ports', $yamlStructure['mysql_env']);
        $this->assertEquals('mysql:latest', $yamlStructure['mysql_env']['image']);
        $this->assertEquals('mysql_env', $yamlStructure['mysql_env']['container_name']);
        $this->assertEquals('MYSQL_ROOT_PASSWORD=anotherveryhardsecret', $yamlStructure['mysql_env']['environment'][0]);
        $this->assertEquals('7162:3306', $yamlStructure['mysql_env']['ports'][0]);
    }
}