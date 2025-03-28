<?php

declare(strict_types=1);

namespace App\ReceiptApp\Receipts;

use App\ReceiptApp\File;
use Symfony\Component\Yaml\Yaml;
use App\ReceiptApp\Receipts\Questions\DebianQuestion;
use App\ReceiptApp\Receipts\Questions\QuestionInterface;
use App\ReceiptApp\Receipts\Interfaces\{
    HttpReportableInterface,
    ReceiptInterface
};

class Debian extends ReceiptCommons implements ReceiptInterface
{
    private QuestionInterface $questions;

    public function __construct()
    {
        $this->questionsPairs = (new DebianQuestion())->getPropertyQuestionPair();
    }

    public function getFiles(): array
    {
        $this->buildYamlStructure();

        return [
            new File("docker-compose.yml", Yaml::dump($this->yamlStructure, 4, 2)),
            new File("Dockerfile", $this->getDockerfile())
        ];
    }

    public function buildYamlStructure(): void
    {
        $this->yamlStructure = [
            'services' => [
                $this->name => [
                    'build' => [
                        'context' => '.'
                    ]
                ]
            ]
        ];
    }

    private function getDockerfile(): string
    {
        return <<<EOF
        FROM debian:bookworm-slim

        RUN apt-get update
        RUN apt-get upgrade -y

        CMD while : ; do sleep 1000; done
        EOF;
    }
}
