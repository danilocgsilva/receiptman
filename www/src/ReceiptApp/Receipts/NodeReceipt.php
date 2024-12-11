<?php

declare(strict_types=1);

namespace App\ReceiptApp\Receipts;

use App\ReceiptApp\File;
use App\ReceiptApp\Receipts\ReceiptInterface;
use Symfony\Component\Yaml\Yaml;
use App\ReceiptApp\Receipts\Questions\QuestionInterface;
use App\ReceiptApp\Receipts\Questions\NodeQuestion;

class NodeReceipt extends ReceiptCommons implements ReceiptInterface
{
    private bool $infinityLoop = false;

    private QuestionInterface $questions;

    private bool $volumeApp = false;

    public function __construct()
    {
        $this->questions = new NodeQuestion();
    }

    /**
     * @inheritDoc
     */
    public function getFiles(): array
    {
        if (!property_exists($this,"name")) {
            throw new NotReadyException();
        }
        
        $this->buildYamlStructure();
        
        $files = [
            new File("docker-compose.yml", Yaml::dump($this->yamlStructure, 4, 2))
        ];

        if ($this->infinityLoop) {
            $files[] = new File('Dockerfile', $this->getDockerfileContent());
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

    private function buildYamlStructure(): void
    {
        $this->yamlStructure = [
            'services' => [
                $this->name => []
            ]
        ];

        if ($this->infinityLoop) {
            $this->yamlStructure['services'][$this->name]['build'] = [
                'context' => '.'
            ];
        } else {
            $this->yamlStructure['services'][$this->name]['image'] = 'node:latest';
        }
        
        $this->yamlStructure['services'][$this->name]['container_name'] = $this->name;

        if ($this->volumeApp) {
            $this->yamlStructure['services'][$this->name]['volumes'][] = './app:/app';
        }

        if ($this->networkModeHost) {
            $this->yamlStructure['services'][$this->name]['network_mode'] = 'host';
        }
    }

    public function getPropertyQuestionsPairs(): array
    {
        return $this->questions->getPropertyQuestionPair();
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
