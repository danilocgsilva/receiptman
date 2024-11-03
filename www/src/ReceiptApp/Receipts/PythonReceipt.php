<?php

namespace App\ReceiptApp\Receipts;

use App\ReceiptApp\File;
use App\ReceiptApp\Receipts\ReceiptInterface;
use Symfony\Component\Yaml\Yaml;
use App\ReceiptApp\Receipts\Questions\QuestionInterface;
use App\ReceiptApp\Receipts\Questions\PythonQuestion;

class PythonReceipt implements ReceiptInterface
{
    private string $name;

    private QuestionInterface $questions;

    public function __construct()
    {
        $this->questions = new PythonQuestion();
    }

    /**
     * @inheritDoc
     */
    public function getFiles(): array
    {
        $this->buildYamlStructure();
        
        return [
            new File("docker-compose.yml", Yaml::dump($this->yamlStructure, 4, 2)),
            new File("Dockerfile", $this->getDockerfile())
        ];
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    private function buildYamlStructure(): void
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
        return $this->questions->getPropertyQuestionPair();
    }

    private function getDockerfile(): string
    {
        return <<<EOF
FROM debian:bookworm-slim

RUN apt-get update
RUN apt-get upgrade -y
RUN apt-get install python3

CMD while : ; do sleep 1000; done
EOF;
    }
}
