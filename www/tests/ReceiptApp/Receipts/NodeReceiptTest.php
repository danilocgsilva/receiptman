<?php

namespace Tests\ReceiptApp\Receipts;

use App\ReceiptApp\Receipts\NodeReceipt;
use PHPUnit\Framework\TestCase;
use App\ReceiptApp\File;

class NodeReceiptTest extends TestCase
{
    private NodeReceipt $nodeReceipt;
    
    function setUp(): void
    {
        $this->nodeReceipt = new NodeReceipt();
    }
    
    public function testCountFiles(): void
    {
        $this->nodeReceipt->setName("my-first-test");
        $this->assertCount(1, $this->nodeReceipt->getFiles());
    }

    public function testFileContent(): void
    {
        $this->nodeReceipt->setName("my-second-test");
        $dockerComposeFile = $this->nodeReceipt->getFiles()[0];

        $expectedFileContent = <<<EOF
services:
  my-second-test:
    image: 'node:latest'
    container_name: my-second-test

EOF;
        $this->assertSame($expectedFileContent, $dockerComposeFile->content);
    }

    public function testReturnType(): void
    {
        $this->nodeReceipt->setName("my-third-test");
        $files = $this->nodeReceipt->getFiles();
        $this->assertInstanceOf(File::class, $files[0]);
    }

    public function testWithLoop(): void
    {
        $this->nodeReceipt->setInfinitLoop();
        $this->nodeReceipt->setName("my-fifith-test");
        $dockerComposeFile = $this->nodeReceipt->getFiles()[0];

        $expectedFileContent = <<<EOF
services:
  my-fifith-test:
    build:
      context: .
    container_name: my-fifith-test

EOF;

        $this->assertSame($expectedFileContent, $dockerComposeFile->content);
    }

    public function testCountFilesIfInfinityLoop(): void
    {
        $this->nodeReceipt->setInfinitLoop();
        $this->nodeReceipt->setName("my-seventh-test");
        $files = $this->nodeReceipt->getFiles();
        $this->assertCount(2, $files);
    }

    public function testSeeDockerfileIfLoop(): void
    {
        $this->nodeReceipt->setInfinitLoop();
        $this->nodeReceipt->setName("my-eithy-test");
        $files = $this->nodeReceipt->getFiles();
        $fileDockerfile = null;
        foreach ($files as $file) {
            if ($file->path === "Dockerfile") {
                $fileDockerfile = $file;
                break;
            }
        }

        $expectedFileContent = <<<EOF
FROM node:latest

CMD while : ; do sleep 1000; done
EOF;

        $this->assertSame($expectedFileContent, $fileDockerfile->content);
    }
}
