<?php

declare(strict_types=1);

namespace App\ReceiptApp\Receipts;

use App\ReceiptApp\File;
use App\ReceiptApp\Receipts\Questions\DotNetQuestions;
use Symfony\Component\Yaml\Yaml;
use App\ReceiptApp\Receipts\Interfaces\ReceiptInterface;
use App\ReceiptApp\Traits\PutGenericDatabase;
use App\ReceiptApp\Receipts\Questions\Types\QuestionEntry;
use Symfony\Component\Filesystem\Filesystem;
use App\Utilities\DockerReceiptWritter;

class DotNetReceipt extends ReceiptCommons implements ReceiptInterface
{
    use PutGenericDatabase;

    private bool $hostMountVolume = false;

    private bool $database = false;

    private string $mysqlRootPassword;

    private string $mysqlPortRedirection;

    public function __construct(Filesystem $fs)
    {
        parent::__construct($fs);
        $this->questionsPairs = (new DotNetQuestions())->getPropertyQuestionPair();
    }

    public function getFiles(): array
    {
        return [
            new File("Dockerfile", $this->getDockerfileContent(), $this->fs)
        ];
    }

    public function buildYamlStructure(): void
    {
        $this->yamlStructure = [
            $this->name => [
                'build' => [
                    'context' => '.'
                ],
                'container_name' => $this->name
            ]
        ];

        if ($this->hostMountVolume) {
            $this->yamlStructure[$this->name]['volumes'][] = './app:/app';
        }

        if ($this->database) {
            $this->putGenericDatabase();
        }
    }

    public function setHostMountVolume(): static
    {
        $this->hostMountVolume = true;
        return $this;
    }

    public function setMysqlPortRedirection(string $portRedirection): static
    {
        $this->mysqlPortRedirection = $portRedirection;
        return $this;
    }

    public function setMysqlRootPassword(string $mysqlRootPassword): static
    {
        $this->mysqlRootPassword = $mysqlRootPassword;
        return $this;
    }

    public function setDatabase(): static
    {
        $this->database = true;

        $this->questionsPairs = array_merge(
            $this->questionsPairs,
            [
                new QuestionEntry(
                    methodName: "setMysqlPortRedirection",
                    textQuestion: "Write the port number redirection for mysql\n"
                )
            ],
            [
                new QuestionEntry(
                    methodName: "setMysqlRootPassword",
                    textQuestion: "Write the mysql root password\n"
                )
            ]
        );

        return $this;
    }

    private function getDockerfileContent(): string
    {
        $dockerfileWritter = new DockerReceiptWritter();

        $dockerfileWritter->addRawContent("FROM mcr.microsoft.com/dotnet/sdk:8.0");
        $dockerfileWritter->addBlankLine();
        $dockerfileWritter->addRawContent("CMD while : ; do sleep 1000; done");
        return $dockerfileWritter->dump();
    }
}
