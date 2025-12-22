<?php

declare(strict_types=1);

namespace App\Tests\ReceiptApp\Receipts;

use App\ReceiptApp\Receipts\PostgreReceipt;
use PHPUnit\Framework\TestCase;
use App\Tests\Traits\GetSpecificFileTrait;
use PHPUnit\Framework\Attributes\Test;
use App\Tests\Traits\MockFileSystemTrait;

class PostgreReceiptTest extends TestCase
{
    use GetSpecificFileTrait;
    use MockFileSystemTrait;

    private PostgreReceipt $postgreReceipt;
    
    function setUp(): void
    {
        $this->postgreReceipt = new PostgreReceipt(
            $this->getFileSystemMocked("", 0)
        );
    }

    #[Test]
    public function basic()
    {
        $this->postgreReceipt->setName("my_first_test_postgre_container");
        $this->postgreReceipt->setDatabaseRootPassword("my_postgres_strong_password");
        
        // $expectedContent =<<<EOF
        // services:
        //   my_first_test_postgre_container:
        //     image: postgres
        //     container_name: my_first_test_postgre_container
        //     ports:
        //       - '5432:5432'
        //     environment:
        //       POSTGRES_PASSWORD: my_postgres_strong_password
    
        // EOF;

        // $fileDockerfile = $this->getSpecificFile($this->postgreReceipt->getFiles(), "docker-compose.yml");

        // $this->assertSame($expectedContent, $fileDockerfile->content);

        $yamlServiceStructure = $this->postgreReceipt->getServiceYamlStructure();
        $this->assertIsArray($yamlServiceStructure);
        $this->assertArrayHasKey("my_first_test_postgre_container", $yamlServiceStructure);
        $this->assertArrayHasKey("image", $yamlServiceStructure["my_first_test_postgre_container"]);
        $this->assertArrayHasKey("container_name", $yamlServiceStructure["my_first_test_postgre_container"]);
        $this->assertArrayHasKey("ports", $yamlServiceStructure["my_first_test_postgre_container"]);
        $this->assertArrayHasKey("environment", $yamlServiceStructure["my_first_test_postgre_container"]);
        $this->assertSame("postgres", $yamlServiceStructure["my_first_test_postgre_container"]["image"]);
        $this->assertSame("my_first_test_postgre_container", $yamlServiceStructure["my_first_test_postgre_container"]["container_name"]);
        $this->assertSame("5432:5432", $yamlServiceStructure["my_first_test_postgre_container"]["ports"][0]);
        $this->assertArrayHasKey("POSTGRES_PASSWORD", $yamlServiceStructure["my_first_test_postgre_container"]["environment"]);
        $this->assertSame("my_postgres_strong_password", $yamlServiceStructure["my_first_test_postgre_container"]["environment"]["POSTGRES_PASSWORD"]);
    }
}
