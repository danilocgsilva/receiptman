<?php

declare(strict_types=1);

namespace App\ReceiptApp\Receipts;

use App\ReceiptApp\File;
use App\ReceiptApp\Receipts\Questions\BaseQuestion;
use Symfony\Component\Yaml\Yaml;

class NginxReceipt extends ReceiptCommons implements ReceiptInterface
{
    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getFiles(): array
    {
        if (!isset($this->name)) {
            throw new NotReadyException();
        }
        
        $this->buildYamlStructure();

        return [
            new File("docker-compose.yml", Yaml::dump($this->yamlStructure, 4, 2)),
        ];
    }

    public function buildYamlStructure(): void
    {
        $this->yamlStructure = [
            'services' => [
                $this->name => [
                    'image' => 'nginx:latest',
                    'container_name' => $this->name
                ]
            ]
        ];
    }

    public function getPropertyQuestionsPairs(): array
    {
        $questionsPairs = new BaseQuestion();
        return $questionsPairs->getPropertyQuestionPair();
    }
}

