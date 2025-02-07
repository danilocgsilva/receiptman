<?php

declare(strict_types=1);

namespace App\ReceiptApp\Receipts;

use App\ReceiptApp\Receipts\Interfaces\ReceiptInterface;
use App\ReceiptApp\File;
use Symfony\Component\Yaml\Yaml;

class PostgreReceipt extends ReceiptCommons implements ReceiptInterface
{
    /**
     * @inheritDoc
     */
    public function getFiles(): array
    {
        $this->buildYamlStructure();

        $files = [
            new File("docker-compose.yml", Yaml::dump($this->yamlStructure, 4, 2))
        ];
        
        return $files;
    }

    private function buildYamlStructure(): void
    {
        $this->yamlStructure = [
            'services' => [
                $this->name => [
                    'image' => 'postgres',
                    'container_name' => $this->name,
                    'environment' => [
                        "POSTGRES_PASSWORD" => "postgresstrongpassword"
                    ]
                ]
            ]
        ];
    }
}