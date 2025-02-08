<?php

declare(strict_types=1);

namespace App\Tests\ReceiptApp\Receipts;

use App\ReceiptApp\Receipts\PostgreReceipt;
use PHPUnit\Framework\TestCase;
use App\Tests\Traits\GetSpecificFileTrait;
use PHPUnit\Framework\Attributes\Test;


class PostgreReceiptTest extends TestCase
{
    use GetSpecificFileTrait;

    private PostgreReceipt $postgreReceipt;
    
    function setUp(): void
    {
        $this->postgreReceipt = new PostgreReceipt();
    }

    #[Test]
    public function basic()
    {
        $this->postgreReceipt->setName("my_first_test_postgre_container");
        $this->postgreReceipt->setDatabaseRootPassword("my_postgres_strong_password");
        
        $expectedContent =<<<EOF
        services:
          my_first_test_postgre_container:
            image: postgres
            container_name: my_first_test_postgre_container
            ports:
              - '5432:5432'
            environment:
              POSTGRES_PASSWORD: my_postgres_strong_password
    
        EOF;

        $fileDockerfile = $this->getSpecificFile($this->postgreReceipt->getFiles(), "docker-compose.yml");

        $this->assertSame($expectedContent, $fileDockerfile->content);
    }
}
