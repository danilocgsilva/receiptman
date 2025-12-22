<?php

declare(strict_types=1);

namespace App\ReceiptApp\Receipts;

use App\ReceiptApp\File;
use App\ReceiptApp\Receipts\Interfaces\ReceiptInterface;
use Symfony\Component\Yaml\Yaml;
use App\ReceiptApp\Receipts\Questions\QuestionInterface;
use App\ReceiptApp\Receipts\Questions\PythonQuestion;
use Symfony\Component\Filesystem\Filesystem;
use App\Utilities\DockerReceiptWritter;

class PythonReceipt extends ReceiptCommons implements ReceiptInterface
{
    private QuestionInterface $questions;

    private bool $pip = false;

    private bool $installGit = false;

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
            new File("Dockerfile", $this->getDockerfile(), $this->fs)
        ];
    }

    public function setPip(): self
    {
        $this->pip = true;
        return $this;
    }

    public function setInstallGit(): self
    {
        $this->installGit = true;
        return $this;
    }

    private function buildYamlStructure(): void
    {
        $this->yamlStructure = [
            $this->name => [
                'build' => [
                    'context' => '.'
                ],
                'container_name' => $this->name
            ]
        ];
    }

    public function getPropertyQuestionsPairs(): array
    {
        return $this->questionsPairs;
    }

    private function getDockerfile(): string
    {
        $dockerfileWritter = new DockerReceiptWritter();
        $dockerfileWritter->addRawContent("FROM debian:bookworm-slim");
        $dockerfileWritter->addBlankLine();
        $dockerfileWritter->addAptGetUpdate();
        $dockerfileWritter->addAptGetUpgrade();
        $dockerfileWritter->addRawContent("RUN apt-get install python3 -y");
        if ($this->pip) {
            $dockerfileWritter->addRawContent("RUN apt-get install python3-pip -y");
        }

        if ($this->installGit) {
            $dockerfileWritter->addRawContent("RUN apt-get install git -y");
        }
        $dockerfileWritter->addBlankLine();
        $dockerfileWritter->addRawContent("CMD while : ; do sleep 1000; done");

        return $dockerfileWritter->dump();
    }
}
