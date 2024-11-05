<?php

namespace App\Tests\ReceiptApp\Receipts;

use App\ReceiptApp\Receipts\PhpDevMysql;
use PHPUnit\Framework\TestCase;
use App\Tests\Traits\GetSpecificFileTrait;

class PhpDevMysqlTest extends TestCase
{
    use GetSpecificFileTrait;

    private PhpDevMysql $phpDevMysql;
    
    function setUp(): void
    {
        $this->phpDevMysql = new PhpDevMysql();
    }

    public function testDockerComposeContent(): void
    {
        $this->phpDevMysql->setName("testing_env")
            ->setHttpPortRedirection("6000")
            ->setMysqlPortRedirection("4006")
            ->setMysqlRootPassword("testing_password");

$expectedContent = <<<EOF
services:
  testing_env:
    build:
      context: .
    container_name: testing_env
    volumes:
      - './www:/var/www'
    ports:
      - '6000:80'
    working_dir: /var/www
  testing_env_db:
    image: 'mysql:latest'
    container_name: testing_env_db
    environment:
      - MYSQL_ROOT_PASSWORD=testing_password
    ports:
      - '4006:3306'

EOF;

        $fileDockerfile = $this->getSpecificFile($this->phpDevMysql->getFiles(), "docker-compose.yml");

        $this->assertSame($expectedContent, $fileDockerfile->content);
    }

    public function testAppFolder(): void
    {
        $this->phpDevMysql->setName("testing_env")
            ->setHttpPortRedirection("6000")
            ->setMysqlPortRedirection("4006")
            ->setMysqlRootPassword("testing_password")
            ->setAppFolder();

$expectedContent = <<<EOF
services:
  testing_env:
    build:
      context: .
    container_name: testing_env
    volumes:
      - './app:/app'
    ports:
      - '6000:80'
    working_dir: /app
  testing_env_db:
    image: 'mysql:latest'
    container_name: testing_env_db
    environment:
      - MYSQL_ROOT_PASSWORD=testing_password
    ports:
      - '4006:3306'

EOF;

        $fileDockerfile = $this->getSpecificFile($this->phpDevMysql->getFiles(), "docker-compose.yml");

        $this->assertSame($expectedContent, $fileDockerfile->content);
    }
}
