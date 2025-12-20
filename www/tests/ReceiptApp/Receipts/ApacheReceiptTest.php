<?php

declare(strict_types=1);

namespace App\Tests\ReceiptApp\Receipts;

use App\Tests\Traits\GetSpecificFileTrait;
use PHPUnit\Framework\TestCase;
use App\ReceiptApp\Receipts\ApacheReceipt;
use App\ReceiptApp\File;
use App\Tests\Traits\MockFileSystemTrait;

class ApacheReceiptTest extends TestCase
{
    use GetSpecificFileTrait;
    use MockFileSystemTrait;

    private ApacheReceipt $apacheReceipt;
    
    function setUp(): void
    {
        $this->apacheReceipt = new ApacheReceipt(
            $this->getFileSystemMocked("", 0)
        );
    }

    public function testDockerFileContent(): void
    {
        $this->apacheReceipt->setName("apache_env");

        $yamlStructure = $this->apacheReceipt->getServiceYamlStructure();

        $this->assertIsArray($yamlStructure);
        $this->assertArrayHasKey("apache_env", $yamlStructure);
        $this->assertArrayHasKey("image", $yamlStructure['apache_env']);
        $this->assertArrayHasKey("container_name", $yamlStructure['apache_env']);
        $this->assertSame("httpd:latest", $yamlStructure['apache_env']["image"]);
        $this->assertSame("apache_env", $yamlStructure['apache_env']["container_name"]);
    }

    public function testDockerFileWithRedirection(): void
    {
        $this->apacheReceipt->setName("apache_redirect");
        $this->apacheReceipt->setHttpPortRedirection("80");

        $yamlStructure = $this->apacheReceipt->getServiceYamlStructure();

        $this->assertIsArray($yamlStructure);
        $this->assertArrayHasKey("apache_redirect", $yamlStructure);
        $this->assertArrayHasKey("image", $yamlStructure["apache_redirect"]);
        $this->assertArrayHasKey("container_name", $yamlStructure["apache_redirect"]);
        $this->assertArrayHasKey("ports", $yamlStructure["apache_redirect"]);
        $this->assertSame("httpd:latest", $yamlStructure["apache_redirect"]["image"]);
        $this->assertSame("apache_redirect", $yamlStructure["apache_redirect"]["container_name"]);
        $this->assertSame(['80:80'], $yamlStructure["apache_redirect"]["ports"]);
    }

    public function testDockerFileWwwRedirection(): void
    {
        $this->apacheReceipt->setName("apache_ewww");
        $this->apacheReceipt->onExposeWWW();

        $yamlStructure = $this->apacheReceipt->getServiceYamlStructure();

        $this->assertIsArray($yamlStructure);
        $this->assertArrayHasKey("apache_ewww", $yamlStructure);
        $this->assertArrayHasKey("image", $yamlStructure["apache_ewww"]);
        $this->assertArrayHasKey("volumes", $yamlStructure["apache_ewww"]);
        $this->assertSame("httpd:latest", $yamlStructure["apache_ewww"]["image"]);
        $this->assertSame("./html:/var/www/html", $yamlStructure["apache_ewww"]["volumes"][0]);

    }

    public function testDockerComposeFileContentWithHostMode(): void
    {
        $this->apacheReceipt->setName("apache_ewww");
        $this->apacheReceipt->setNetworkModeHost();

        $yamlStructure = $this->apacheReceipt->getServiceYamlStructure();

        $this->assertIsArray($yamlStructure);
        $this->assertArrayHasKey("apache_ewww", $yamlStructure);
        $this->assertArrayHasKey("network_mode", $yamlStructure["apache_ewww"]);
        $this->assertSame("httpd:latest", $yamlStructure["apache_ewww"]["image"]);
        $this->assertSame("apache_ewww", $yamlStructure["apache_ewww"]["container_name"]);
        $this->assertSame("host", $yamlStructure["apache_ewww"]["network_mode"]);
    }
}
