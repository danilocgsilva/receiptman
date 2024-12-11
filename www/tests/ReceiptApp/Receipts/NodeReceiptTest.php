<?php

declare(strict_types=1);

namespace App\Tests\ReceiptApp\Receipts;

use App\ReceiptApp\Receipts\NodeReceipt;
use App\ReceiptApp\Receipts\Questions\QuestionEntry;
use PHPUnit\Framework\TestCase;
use App\ReceiptApp\File;
use App\Tests\Traits\GetSpecificFileTrait;

class NodeReceiptTest extends TestCase
{
    use GetSpecificFileTrait;

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

        $fileDockerfile = $this->getSpecificFile($files, "Dockerfile");

        $expectedFileContent = <<<EOF
FROM node:latest

CMD while : ; do sleep 1000; done
EOF;

        $this->assertSame($expectedFileContent, $fileDockerfile->content);
    }

    public function testAppVolume(): void
    {
        $this->nodeReceipt->setName("must_have_volume_app");
        $this->nodeReceipt->setVolumeApp();

        $expectedFileContent = <<<EOF
services:
  must_have_volume_app:
    image: 'node:latest'
    container_name: must_have_volume_app
    volumes:
      - './app:/app'

EOF;

        $receiptFiles = $this->nodeReceipt->getFiles();
        $dockerCompose = $this->getSpecificFile($receiptFiles, 'docker-compose.yml');

        $this->assertSame($expectedFileContent, $dockerCompose->content);
    }

    public function testAppVolumeAndLoop(): void
    {
        $expectedFileContent = <<<EOF
services:
  name_name:
    build:
      context: .
    container_name: name_name
    volumes:
      - './app:/app'

EOF;

        $this->nodeReceipt->setName("name_name");
        $this->nodeReceipt->setVolumeApp();
        $this->nodeReceipt->setInfinitLoop();

        $receiptFiles = $this->nodeReceipt->getFiles();
        $dockerCompose = $this->getSpecificFile($receiptFiles, 'docker-compose.yml');

        $this->assertSame($expectedFileContent, $dockerCompose->content);
    }

    public function testNetworkModeHost(): void
    {
        $expectedFileContent = <<<EOF
services:
  network_host_mode_container:
    image: 'node:latest'
    container_name: network_host_mode_container
    network_mode: host

EOF;

        $this->nodeReceipt->setName("network_host_mode_container");
        $this->nodeReceipt->setNetworkModeHost();

        $receiptFiles = $this->nodeReceipt->getFiles();
        $dockerCompose = $this->getSpecificFile($receiptFiles, 'docker-compose.yml');
        $this->assertSame($expectedFileContent, $dockerCompose->content);
    }

    public function testTypeOfPropertiesQuestionPairs(): void
    {
        $questionsParis = $this->nodeReceipt->getPropertyQuestionsPairs();
        $this->assertInstanceOf(
            expected: QuestionEntry::class, 
            actual: $questionsParis[0]
        );
    }
}
