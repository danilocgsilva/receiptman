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

        $dockerComposeFileContent = <<<EOF
        services:
          mysql_env:
            image: 'mysql:latest'
            container_name: mysql_env
        
        EOF;

        $dockerComposeFile = $this->mySQLReceipt->getFiles();
        $dockerComposeFile = $this->getSpecificFile($dockerComposeFile, "docker-compose.yml");

        $this->assertInstanceOf(File::class, $dockerComposeFile);
        $this->assertSame($dockerComposeFileContent, $dockerComposeFile->content);
    }
}
