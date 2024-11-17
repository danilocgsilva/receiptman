<?php

declare(strict_types=1);

namespace App\Tests\ReceiptApp\Receipts;

use App\Tests\Traits\GetSpecificFileTrait;
use PHPUnit\Framework\TestCase;
use App\ReceiptApp\Receipts\NginxReceipt;

class NginxReceiptTest extends TestCase
{
    use GetSpecificFileTrait;

    private NginxReceipt $nginxReceipt;
    
    function setUp(): void
    {
        $this->nginxReceipt = new NginxReceipt();
    }

    public function testDockerComposeFileContent(): void
    {
        $this->nginxReceipt->setName("nginx_env");
        $dockerComposeFile = $this->nginxReceipt->getFiles()[0];

        $expectedFileContent = <<<EOF
services:
  nginx_env:
    image: 'nginx:latest'
    container_name: nginx_env

EOF;

        $this->assertSame($expectedFileContent, $dockerComposeFile->content);
    }
}
