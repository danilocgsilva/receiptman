<?php

declare(strict_types=1);

namespace App\Tests;

use App\Utilities\DockerReceiptWritter;
use PHPUnit\Framework\TestCase;

class DockerReceiptWritterTest extends TestCase
{
    private DockerReceiptWritter $dockerReceiptWritter;

    public function setUp(): void
    {
        $this->dockerReceiptWritter = new DockerReceiptWritter();
    }

    public function testDumpNoContent()
    {
        $this->assertSame("", $this->dockerReceiptWritter->dump());
    }

    public function testAddBlankLine()
    {
        $this->dockerReceiptWritter->addBlankLine();
        $expectedContent =<<<EOF
        
        EOF;
        $this->assertSame($expectedContent, $this->dockerReceiptWritter->dump());
    }

    public function testAddRawContentAny(): void
    {
        $rawContent = "This is a raw content line";
        $this->dockerReceiptWritter->addRawContent($rawContent);
        $this->assertSame($rawContent, $this->dockerReceiptWritter->dump());
    }

    public function testAddRawContentFrom(): void
    {
        $rawContent = "FROM php:8.4.7-apache-bookworm";
        $this->dockerReceiptWritter->addRawContent($rawContent);
        $this->assertSame("FROM php:8.4.7-apache-bookworm", $this->dockerReceiptWritter->dump());
    }

    public function testSimpleDockerReceipt(): void
    {
        $this->dockerReceiptWritter->addRawContent("FROM php:8.4.7-apache-bookworm");
        $this->dockerReceiptWritter->addBlankLine();
        $this->dockerReceiptWritter->addRawContent("RUN apt-get update");
        $this->dockerReceiptWritter->addRawContent("RUN apt-get upgrade");
        
        $expectedContent = <<<EOF
        FROM php:8.4.7-apache-bookworm

        RUN apt-get update
        RUN apt-get upgrade
        EOF;

        $this->assertSame($expectedContent, $this->dockerReceiptWritter->dump());
    }

    public function testSimpleDockerReceiptSpecificCommands(): void
    {
        $this->dockerReceiptWritter->addRawContent("FROM php:8.4.7-apache-bookworm");
        $this->dockerReceiptWritter->addBlankLine();
        $this->dockerReceiptWritter->addAptGetUpdate();
        $this->dockerReceiptWritter->addAptGetUpgrade();

        $expectedContent = <<<EOF
        FROM php:8.4.7-apache-bookworm

        RUN apt-get update
        RUN apt-get upgrade -y
        EOF;

        $this->assertSame($expectedContent, $this->dockerReceiptWritter->dump());
    }

    public function testSimpleDockerReceiptFluentInterface(): void
    {
        $this->dockerReceiptWritter->addRawContent("FROM php:8.4.7-apache-bookworm")
            ->addBlankLine()
            ->addAptGetUpdate()
            ->addAptGetUpgrade();

        $expectedContent = <<<EOF
        FROM php:8.4.7-apache-bookworm

        RUN apt-get update
        RUN apt-get upgrade -y
        EOF;

        $this->assertSame($expectedContent, $this->dockerReceiptWritter->dump());
    }

    public function testAddBlankLineMiddleReceipt(): void
    {
        $this->dockerReceiptWritter->addRawContent("FROM php:8.4.7-apache-bookworm")
            ->addBlankLine()
            ->addAptGetUpdate()
            ->addBlankLine()
            ->addAptGetUpgrade();

        $expectedContent = <<<EOF
        FROM php:8.4.7-apache-bookworm

        RUN apt-get update

        RUN apt-get upgrade -y
        EOF;

        $this->assertSame($expectedContent, $this->dockerReceiptWritter->dump());
    }

    public function testAddInstallPackages(): void
    {
        $packages = ['curl', 'git', 'vim'];
        $this->dockerReceiptWritter->addInstallPackages($packages);

        $expectedContent = "RUN apt-get install -y curl git vim";
        $this->assertSame($expectedContent, $this->dockerReceiptWritter->dump());
    }
}
