<?php

declare(strict_types=1);

namespace App\ReceiptApp\Receipts;

use App\ReceiptApp\File;
use Symfony\Component\Yaml\Yaml;
use App\ReceiptApp\Receipts\Questions\DebianQuestion;
use App\ReceiptApp\Receipts\Interfaces\ReceiptInterface;
use Symfony\Component\Filesystem\Filesystem;
use App\Utilities\DockerReceiptWritter;

class DebianReceipt extends ReceiptCommons implements ReceiptInterface
{
    public function __construct(Filesystem $fs)
    {
        parent::__construct($fs);
        $this->questionsPairs = (new DebianQuestion())->getPropertyQuestionPair();
    }

    public function getFiles(): array
    {
        // $this->buildYamlStructure();

        return [
            // new File("docker-compose.yml", Yaml::dump($this->yamlStructure, 4, 2), $this->fs),
            new File("Dockerfile", $this->getDockerfile(), $this->fs)
        ];
    }

    public function buildYamlStructure(): void
    {
        $this->yamlStructure = [
            $this->name => [
                'build' => [
                    'context' => '.'
                ]
            ]
        ];
    }

    private function getDockerfile(): string
    {
        $dockerfileWritter = new DockerReceiptWritter();
        $dockerfileWritter->addRawContent("FROM debian:bookworm-slim");
        $dockerfileWritter->addBlankLine();
        $dockerfileWritter->addAptGetUpdate();
        $dockerfileWritter->addAptGetUpgrade();
        $dockerfileWritter->addBlankLine();
        $dockerfileWritter->addRawContent("CMD while : ; do sleep 1000; done");

        return $dockerfileWritter->dump();
    }
}
