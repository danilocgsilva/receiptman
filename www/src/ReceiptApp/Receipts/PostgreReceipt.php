<?php

declare(strict_types=1);

namespace App\ReceiptApp\Receipts;

use App\ReceiptApp\Receipts\Interfaces\ReceiptInterface;
use App\ReceiptApp\File;
use Symfony\Component\Yaml\Yaml;
use App\ReceiptApp\Receipts\Questions\PostgreQuestions;
use App\ReceiptApp\Traits\DatabaseRootPasswordReceiptTrait;
class PostgreReceipt extends ReceiptCommons implements ReceiptInterface
{
    use DatabaseRootPasswordReceiptTrait;
    
    public function __construct()
    {
        $this->questionsPairs = (new PostgreQuestions())->getPropertyQuestionPair();
    }

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
                    'ports' => [
                        '5432:5432'
                    ],
                    'environment' => [
                        "POSTGRES_PASSWORD" => $this->databaseRootPassword
                    ]
                ]
            ]
        ];
    }
}