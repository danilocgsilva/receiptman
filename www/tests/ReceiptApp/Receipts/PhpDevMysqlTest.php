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

    public function testSsh(): void
    {
        $this->phpDevMysql->setName("testing_env2")
            ->setHttpPortRedirection("4000")
            ->setMysqlPortRedirection(mysqlPortRedirection: "3333")
            ->setMysqlRootPassword("opass2")
            ->setSshVolume();

        $fileDockerCompose = $this->getSpecificFile($this->phpDevMysql->getFiles(), "docker-compose.yml");

        $expectedContent = <<<EOF
services:
  testing_env2:
    build:
      context: .
    container_name: testing_env2
    volumes:
      - './www:/var/www'
      - './.ssh/:/root/.ssh'
    ports:
      - '4000:80'
    working_dir: /var/www
  testing_env2_db:
    image: 'mysql:latest'
    container_name: testing_env2_db
    environment:
      - MYSQL_ROOT_PASSWORD=opass2
    ports:
      - '3333:3306'

EOF;

        $this->assertSame($expectedContent, $fileDockerCompose->content);
    }
}
