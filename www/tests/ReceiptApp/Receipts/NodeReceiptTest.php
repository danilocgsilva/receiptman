<?php

declare(strict_types=1);

namespace App\Tests\ReceiptApp\Receipts;

use App\ReceiptApp\Receipts\NodeReceipt;
use App\ReceiptApp\Receipts\Questions\Types\QuestionEntry;
use PHPUnit\Framework\TestCase;
use App\Tests\Traits\GetSpecificFileTrait;
use App\Tests\Traits\MockFileSystemTrait;

class NodeReceiptTest extends TestCase
{
    use GetSpecificFileTrait;
    use MockFileSystemTrait;

    private NodeReceipt $nodeReceipt;
    
    function setUp(): void
    {
        $this->nodeReceipt = new NodeReceipt(
            $this->getFileSystemMocked("", 0)
        );
    }
    
    public function testCountFiles(): void
    {
        $this->nodeReceipt->setName("my-first-test");
        $this->assertCount(0, $this->nodeReceipt->getFiles());
    }

    public function testFileContent(): void
    {
//         $this->nodeReceipt->setName("my-second-test");
//         $dockerComposeFile = $this->nodeReceipt->getFiles()[0];

//         $expectedFileContent = <<<EOF
// services:
//   my-second-test:
//     image: 'node:latest'
//     container_name: my-second-test

// EOF;
        // $this->assertSame($expectedFileContent, $dockerComposeFile->content);

        $this->nodeReceipt->setName("my-second-test");
        
        $yamlServiceStructure = $this->nodeReceipt->getServiceYamlStructure();
        $this->assertIsArray($yamlServiceStructure);
        $this->assertArrayHasKey('my-second-test', $yamlServiceStructure);
        $this->assertArrayHasKey('image', $yamlServiceStructure['my-second-test']);
        $this->assertArrayHasKey('container_name', $yamlServiceStructure['my-second-test']);
        $this->assertSame('node:latest', $yamlServiceStructure['my-second-test']['image']);
        $this->assertSame('my-second-test', $yamlServiceStructure['my-second-test']['container_name']);
    }

    public function testWithLoop(): void
    {
        $this->nodeReceipt->setInfinitLoop();
        $this->nodeReceipt->setName("my-fifith-test");
        // $dockerComposeFile = $this->nodeReceipt->getFiles()[0];

//         $expectedFileContent = <<<EOF
// services:
//   my-fifith-test:
//     build:
//       context: .
//     container_name: my-fifith-test

// EOF;

//         $this->assertSame($expectedFileContent, $dockerComposeFile->content);

        $yamlServiceStructure = $this->nodeReceipt->getServiceYamlStructure();
        $this->assertIsArray($yamlServiceStructure);
        $this->assertArrayHasKey('my-fifith-test', $yamlServiceStructure);
        $this->assertArrayHasKey('build', $yamlServiceStructure['my-fifith-test']);
        $this->assertArrayHasKey('container_name', $yamlServiceStructure['my-fifith-test']);
        $this->assertArrayHasKey('context', $yamlServiceStructure['my-fifith-test']['build']);
        $this->assertSame('.', $yamlServiceStructure['my-fifith-test']['build']['context']);
        $this->assertSame('my-fifith-test', $yamlServiceStructure['my-fifith-test']['container_name']);
    }

    public function testCountFilesIfInfinityLoop(): void
    {
        $this->nodeReceipt->setInfinitLoop();
        $this->nodeReceipt->setName("my-seventh-test");
        $files = $this->nodeReceipt->getFiles();
        $this->assertCount(1, $files);
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

        // @todo transformar este teste em um teste para o objeto WrapServicesYamlStructureTest
//         $expectedFileContent = <<<EOF
// services:
//   must_have_volume_app:
//     image: 'node:latest'
//     container_name: must_have_volume_app
//     volumes:
//       - './app:/app'

// EOF;

//         $receiptFiles = $this->nodeReceipt->getFiles();
//         $dockerCompose = $this->getSpecificFile($receiptFiles, 'docker-compose.yml');

//         $this->assertSame($expectedFileContent, $dockerCompose->content);
        $yamlServiceStructure = $this->nodeReceipt->getServiceYamlStructure();
        $this->assertIsArray($yamlServiceStructure);
        $this->assertArrayHasKey('must_have_volume_app', $yamlServiceStructure);
        $this->assertArrayHasKey('image', $yamlServiceStructure['must_have_volume_app']);
        $this->assertArrayHasKey('container_name', $yamlServiceStructure['must_have_volume_app']);
        $this->assertArrayHasKey('volumes', $yamlServiceStructure['must_have_volume_app']);
        $this->assertSame('node:latest', $yamlServiceStructure['must_have_volume_app']['image']);
        $this->assertSame('must_have_volume_app', $yamlServiceStructure['must_have_volume_app']['container_name']);
        $this->assertSame('./app:/app', $yamlServiceStructure['must_have_volume_app']['volumes'][0]);
    }

    public function testAppVolumeAndLoop(): void
    {
        // @todo transformar este teste em um teste para o objeto WrapServicesYamlStructureTest

//         $expectedFileContent = <<<EOF
// services:
//   name_name:
//     build:
//       context: .
//     container_name: name_name
//     volumes:
//       - './app:/app'

// EOF;

//         $this->nodeReceipt->setName("name_name");
//         $this->nodeReceipt->setVolumeApp();
//         $this->nodeReceipt->setInfinitLoop();

//         $receiptFiles = $this->nodeReceipt->getFiles();
//         $dockerCompose = $this->getSpecificFile($receiptFiles, 'docker-compose.yml');

//         $this->assertSame($expectedFileContent, $dockerCompose->content);
        $this->nodeReceipt->setName("name_name");
        $this->nodeReceipt->setVolumeApp();
        $this->nodeReceipt->setInfinitLoop();

        $yamlServiceStructure = $this->nodeReceipt->getServiceYamlStructure();
        $this->assertIsArray($yamlServiceStructure);
        $this->assertArrayHasKey('name_name', $yamlServiceStructure);
        $this->assertArrayHasKey('build', $yamlServiceStructure['name_name']);
        $this->assertArrayHasKey('container_name', $yamlServiceStructure['name_name']);
        $this->assertArrayHasKey('volumes', $yamlServiceStructure['name_name']);
        $this->assertSame('name_name', $yamlServiceStructure['name_name']['container_name']);
        $this->assertSame('./app:/app', $yamlServiceStructure['name_name']['volumes'][0]);
    }

    public function testNetworkModeHost(): void
    {
        // @todo transformar este teste em um teste para o objeto WrapServicesYamlStructureTest

//         $expectedFileContent = <<<EOF
// services:
//   network_host_mode_container:
//     image: 'node:latest'
//     container_name: network_host_mode_container
//     network_mode: host

// EOF;

        // $this->nodeReceipt->setName("network_host_mode_container");
        // $this->nodeReceipt->setNetworkModeHost();

        // $receiptFiles = $this->nodeReceipt->getFiles();
        // $dockerCompose = $this->getSpecificFile($receiptFiles, 'docker-compose.yml');
        // $this->assertSame($expectedFileContent, $dockerCompose->content);

        $this->nodeReceipt->setName("network_host_mode_container");
        $this->nodeReceipt->setNetworkModeHost();

        $yamlServiceStructure = $this->nodeReceipt->getServiceYamlStructure();
        $this->assertIsArray($yamlServiceStructure);
        $this->assertArrayHasKey('network_host_mode_container', $yamlServiceStructure);
        $this->assertArrayHasKey('image', $yamlServiceStructure['network_host_mode_container']);
        $this->assertArrayHasKey('container_name', $yamlServiceStructure['network_host_mode_container']);
        $this->assertArrayHasKey('network_mode', $yamlServiceStructure['network_host_mode_container']);
        $this->assertSame('node:latest', $yamlServiceStructure['network_host_mode_container']['image']);
        $this->assertSame('network_host_mode_container', $yamlServiceStructure['network_host_mode_container']['container_name']);
        $this->assertSame('host', $yamlServiceStructure['network_host_mode_container']['network_mode']);
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
