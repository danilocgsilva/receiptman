<?php

declare(strict_types=1);

namespace App\Tests\ReceiptApp\Receipts;

use App\ReceiptApp\Receipts\PhpReceipt;
use App\ReceiptApp\Receipts\Questions\Types\QuestionEntry;
use PHPUnit\Framework\TestCase;
use App\Tests\Traits\GetSpecificFileTrait;
use App\Tests\Traits\MockFileSystemTrait;

class PhpReceiptTest extends TestCase
{
    use GetSpecificFileTrait;
    use MockFileSystemTrait;

    private PhpReceipt $phpReceipt;
    
    function setUp(): void
    {
        $this->phpReceipt = new PhpReceipt(
            $this->getFileSystemMocked("", 0)
        );
    }

    public function testServiceYamlStructure(): void
    {
        $this->phpReceipt->setName("php_env");

        $yamlStructure = $this->phpReceipt->getServiceYamlStructure();

        $this->assertIsArray($yamlStructure);
        $this->assertArrayHasKey("php_env", $yamlStructure);
        $this->assertArrayHasKey("image", $yamlStructure['php_env']);
        $this->assertArrayHasKey("container_name", $yamlStructure['php_env']);
        $this->assertSame("php:latest", $yamlStructure['php_env']["image"]);
        $this->assertSame("php_env", $yamlStructure['php_env']["container_name"]);
    }

    public function testSetPhpVersion(): void
    {
        $this->phpReceipt->setName("php_custom");
        $this->phpReceipt->setPhpVersion("8.1");

        $yamlStructure = $this->phpReceipt->getServiceYamlStructure();

        $this->assertIsArray($yamlStructure);
        $this->assertArrayHasKey("php_custom", $yamlStructure);
        $this->assertSame("php:8.1", $yamlStructure['php_custom']["image"]);
        $this->assertSame("php_custom", $yamlStructure['php_custom']["container_name"]);
    }

    public function testNetworkModeHost(): void
    {
        $this->phpReceipt->setName("php_host");
        $this->phpReceipt->setNetworkModeHost();

        $yamlStructure = $this->phpReceipt->getServiceYamlStructure();

        $this->assertIsArray($yamlStructure);
        $this->assertArrayHasKey("php_host", $yamlStructure);
        $this->assertArrayHasKey("network_mode", $yamlStructure["php_host"]);
        $this->assertSame("php:latest", $yamlStructure["php_host"]["image"]);
        $this->assertSame("php_host", $yamlStructure["php_host"]["container_name"]);
        $this->assertSame("host", $yamlStructure["php_host"]["network_mode"]);
    }

    public function testPhpVersion83(): void
    {
        $this->phpReceipt->setName("php_83");
        $this->phpReceipt->setPhpVersion("8.3");

        $yamlStructure = $this->phpReceipt->getServiceYamlStructure();

        $this->assertIsArray($yamlStructure);
        $this->assertArrayHasKey("php_83", $yamlStructure);
        $this->assertSame("php:8.3", $yamlStructure['php_83']["image"]);
        $this->assertSame("php_83", $yamlStructure['php_83']["container_name"]);
    }

    public function testPhpVersion84(): void
    {
        $this->phpReceipt->setName("php_84");
        $this->phpReceipt->setPhpVersion("8.4");

        $yamlStructure = $this->phpReceipt->getServiceYamlStructure();

        $this->assertIsArray($yamlStructure);
        $this->assertArrayHasKey("php_84", $yamlStructure);
        $this->assertSame("php:8.4", $yamlStructure['php_84']["image"]);
        $this->assertSame("php_84", $yamlStructure['php_84']["container_name"]);
    }

    public function testPhpVersion85(): void{
        $this->phpReceipt->setName("php_85");
        $this->phpReceipt->setPhpVersion("8.5");

        $yamlStructure = $this->phpReceipt->getServiceYamlStructure();

        $this->assertIsArray($yamlStructure);
        $this->assertArrayHasKey("php_85", $yamlStructure);
        $this->assertSame("php:8.5.1", $yamlStructure['php_85']["image"]);
        $this->assertSame("php_85", $yamlStructure['php_85']["container_name"]);
    }

    public function testTypeOfPropertiesQuestionPairs(): void
    {
        $questionsParis = $this->phpReceipt->getPropertyQuestionsPairs();
        $this->assertInstanceOf(
            expected: QuestionEntry::class, 
            actual: $questionsParis[0]
        );
    }
}
