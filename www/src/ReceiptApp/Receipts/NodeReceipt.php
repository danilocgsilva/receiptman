<?php

declare(strict_types=1);

namespace App\ReceiptApp\Receipts;

use App\ReceiptApp\File;
use App\ReceiptApp\Receipts\Interfaces\ReceiptInterface;
use Symfony\Component\Yaml\Yaml;
use App\ReceiptApp\Receipts\Questions\QuestionInterface;
use App\ReceiptApp\Receipts\Questions\NodeQuestion;
use Symfony\Component\Filesystem\Filesystem;

class NodeReceipt extends ReceiptCommons implements ReceiptInterface
{
    private bool $infinityLoop = false;

    private QuestionInterface $questions;

    private bool $volumeApp = false;

    public function __construct(Filesystem $fs)
    {
        parent::__construct($fs);
        $this->questionsPairs = (new NodeQuestion())->getPropertyQuestionPair();
    }

    /**
     * @inheritDoc
     */
    public function getFiles(): array
    {
        if (!property_exists($this,"name")) {
            throw new NotReadyException($this);
        }
        
        // $this->buildYamlStructure();
        
        // $files = [
        //     new File("docker-compose.yml", Yaml::dump($this->yamlStructure, 4, 2), $this->fs)
        // ];

        $files = [];

        if ($this->infinityLoop) {
            $files[] = new File('Dockerfile', $this->getDockerfileContent(), $this->fs);
        }

        return $files;
    }

    private function getDockerfileContent(): string
    {
        return <<<EOF
        FROM node:latest

        CMD while : ; do sleep 1000; done
        EOF;
    }

    protected function buildYamlStructure(): void
    {
        $this->yamlStructure = [
            $this->name => []
        ];

        if ($this->infinityLoop) {
            $this->yamlStructure[$this->name]['build'] = [
                'context' => '.'
            ];
        } else {
            $this->yamlStructure[$this->name]['image'] = 'node:latest';
        }

        $this->yamlStructure[$this->name]['container_name'] = $this->name;

        if ($this->volumeApp) {
            $this->yamlStructure[$this->name]['volumes'][] = './app:/app';
        }

        if ($this->networkModeHost) {
            $this->yamlStructure[$this->name]['network_mode'] = 'host';
        }
    }

    public function getPropertyQuestionsPairs(): array
    {
        return $this->questionsPairs;
    }

    public function setInfinitLoop(): void
    {
        $this->infinityLoop = true;
    }

    public function setVolumeApp(): self
    {
        $this->volumeApp = true;
        return $this;
    }
}
