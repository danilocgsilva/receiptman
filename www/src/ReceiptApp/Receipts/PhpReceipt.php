<?php

declare(strict_types=1);

namespace App\ReceiptApp\Receipts;

use App\ReceiptApp\Receipts\Interfaces\ReceiptInterface;
use App\ReceiptApp\File;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Filesystem\Filesystem;

class PhpReceipt extends ReceiptCommons implements ReceiptInterface
{
    public function __construct(Filesystem $fs)
    {
        parent::__construct($fs);
        // For this ReceiptInterface implementation no questions are required so far.
        $this->questionsPairs = [];
    }

    /**
     * @inheritDoc
     */
    public function getFiles(): array
    {
        $this->buildYamlStructure();
        
        $files = [
            new File("docker-compose.yml", Yaml::dump($this->yamlStructure, 4, 2), $this->fs)
        ];

        return $files;
    }

    private function buildYamlStructure(): void
    {
        $this->yamlStructure = [
            'services' => [
                $this->name => [
                    'image' => 'php:latest',
                    'container_name' => $this->name
                ]
            ]
        ];
    }
}
