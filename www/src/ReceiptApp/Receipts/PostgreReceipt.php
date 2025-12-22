<?php

declare(strict_types=1);

namespace App\ReceiptApp\Receipts;

use App\ReceiptApp\Receipts\Interfaces\ReceiptInterface;
use App\ReceiptApp\File;
use Symfony\Component\Yaml\Yaml;
use App\ReceiptApp\Receipts\Questions\PostgreQuestions;
use App\ReceiptApp\Traits\DatabaseRootPasswordReceiptTrait;
use Symfony\Component\Filesystem\Filesystem;

class PostgreReceipt extends ReceiptCommons implements ReceiptInterface
{
    use DatabaseRootPasswordReceiptTrait;
    
    public function __construct(Filesystem $fs)
    {
        parent::__construct($fs);
        $this->questionsPairs = (new PostgreQuestions())->getPropertyQuestionPair();
    }

    /**
     * @inheritDoc
     */
    public function getFiles(): array
    {
        // $this->buildYamlStructure();

        // $files = [
        //     new File("docker-compose.yml", Yaml::dump($this->yamlStructure, 4, 2), $this->fs)
        // ];
        
        // return $files;

        // The only required file is docker-compose.yml, but it will be taylored afterwards.
        return [];
    }

    protected function buildYamlStructure(): void
    {
        $this->yamlStructure = [
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
        ];
    }
}