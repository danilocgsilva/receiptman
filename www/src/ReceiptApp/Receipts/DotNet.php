<?php

declare(strict_types=1);

namespace App\ReceiptApp\Receipts;

use App\ReceiptApp\File;
use Symfony\Component\Yaml\Yaml;
use App\ReceiptApp\Receipts\Questions\BaseQuestion;
use App\ReceiptApp\Receipts\Interfaces\ReceiptInterface;

class DotNet extends ReceiptCommons implements ReceiptInterface
{
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
    }

    public function getPropertyQuestionsPairs(): array
    {
        $question = new BaseQuestion();
        return $question->getPropertyQuestionPair();
    }

    private function getDockerfileContent(): string
    {
        return <<<EOF
        FROM mcr.microsoft.com/dotnet/sdk:8.0

        CMD while : ; do sleep 1000; done

        EOF;
    }
}
