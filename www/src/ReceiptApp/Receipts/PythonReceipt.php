<?php

declare(strict_types=1);

namespace App\ReceiptApp\Receipts;

use App\ReceiptApp\File;
use App\ReceiptApp\Receipts\Interfaces\ReceiptInterface;
use Symfony\Component\Yaml\Yaml;
use App\ReceiptApp\Receipts\Questions\QuestionInterface;
use App\ReceiptApp\Receipts\Questions\PythonQuestion;
use Symfony\Component\Filesystem\Filesystem;

class PythonReceipt extends ReceiptCommons implements ReceiptInterface
{
    private QuestionInterface $questions;

    public function __construct(Filesystem $fs)
    {
        parent::__construct($fs);
        $this->questionsPairs = (new PythonQuestion())->getPropertyQuestionPair();
    }

    /**
     * @inheritDoc
     */
    public function getFiles(): array
    {
        $this->buildYamlStructure();
        
        return [
            new File("docker-compose.yml", Yaml::dump($this->yamlStructure, 4, 2), $this->fs),
            new File("Dockerfile", $this->getDockerfile(), $this->fs)
        ];
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
        return $this->questionsPairs;
    }

    private function getDockerfile(): string
    {
        return <<<EOF
        FROM debian:bookworm-slim

        RUN apt-get update
        RUN apt-get upgrade -y
        RUN apt-get install python3 -y

        CMD while : ; do sleep 1000; done
        EOF;
    }
}
