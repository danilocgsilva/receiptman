<?php

declare(strict_types=1);

namespace App\ReceiptApp\Receipts;

use App\ReceiptApp\File;
use App\ReceiptApp\Receipts\Questions\DotNetQuestions;
use Symfony\Component\Yaml\Yaml;
use App\ReceiptApp\Receipts\Interfaces\ReceiptInterface;
use App\ReceiptApp\Traits\PutGenericDatabase;
use App\ReceiptApp\Receipts\Questions\Types\QuestionEntry;

class DotNet extends ReceiptCommons implements ReceiptInterface
{
    use PutGenericDatabase;

    private bool $hostMountVolume = false;

    private bool $database = false;

    private string $mysqlRootPassword;

    private string $mysqlPortRedirection;

    public function __construct()
    {
        $this->questionsPairs = (new DotNetQuestions())->getPropertyQuestionPair();
    }

    public function getFiles(): array
    {
        $this->buildYamlStructure();

        return [
            new File("docker-compose.yml", Yaml::dump($this->yamlStructure, 4, 2)),
            new File("Dockerfile", $this->getDockerfileContent())
        ];
    }

    public function buildYamlStructure(): void
    {
        $this->yamlStructure = [
            'services' => [
                $this->name => [
                    'build' => [
                        'context' => '.'
                    ],
                    'container_name' => $this->name
                ]
            ]
        ];

        if ($this->hostMountVolume) {
            $this->yamlStructure['services'][$this->name]['volumes'][] = './app:/app';
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
        return <<<EOF
        FROM mcr.microsoft.com/dotnet/sdk:8.0

        CMD while : ; do sleep 1000; done

        EOF;
    }
}
