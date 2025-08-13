<?php

declare(strict_types=1);

namespace App\ReceiptApp\Receipts;

use App\ReceiptApp\Receipts\Interfaces\PhpInterface;
use App\ReceiptApp\Receipts\Interfaces\ReceiptInterface;
use App\ReceiptApp\File;
use App\ReceiptApp\Receipts\Questions\BaseQuestion;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Filesystem\Filesystem;
use App\ReceiptApp\Receipts\Questions\Types\QuestionEntry;

class PhpReceipt extends ReceiptCommons implements ReceiptInterface, PhpInterface
{
    public function __construct(Filesystem $fs)
    {
        parent::__construct($fs);

        $this->questionsPairs = array_merge(
            (new BaseQuestion())->getPropertyQuestionPair(),
            [
                new QuestionEntry(
                    methodName: "setPhpVersion",
                    textQuestion: "Write the PHP version to use \n",
                )
            ]
        );
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

    public function setPhpVersion(string $phpVersion): static
    {
        $this->phpVersion = $phpVersion;
        return $this;
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
