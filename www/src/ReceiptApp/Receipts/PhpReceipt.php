<?php

declare(strict_types=1);

namespace App\ReceiptApp\Receipts;

use App\ReceiptApp\File;
use App\ReceiptApp\Receipts\Interfaces\PhpInterface;
use App\ReceiptApp\Receipts\Interfaces\ReceiptInterface;
use App\ReceiptApp\Receipts\Questions\BaseQuestion;
use Symfony\Component\Filesystem\Filesystem;
use App\ReceiptApp\Receipts\Questions\Types\QuestionEntry;
USE App\ReceiptApp\Receipts\Questions\Types\InputType;

class PhpReceipt extends ReceiptCommons implements ReceiptInterface, PhpInterface
{
    private string $phpVersion = "latest";
    private bool $infinityLoop = false;

    private bool $composer = false;
    
    private array $versionMapping = [
        '8.5' => '8.5.1',
    ];
    
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
            ],
            [
                new QuestionEntry(
                    methodName: "setComposer",
                    textQuestion: "Should I install Composer? \n",
                    inputType: InputType::yesorno
                )
            ]
        );
    }

    public function setComposer(): self
    {
        $this->composer = true;
        return $this;
    }

    public function setPhpVersion(string $phpVersion): static
    {
        $this->phpVersion = $phpVersion;
        return $this;
    }

    public function setInfinitLoop(): void
    {
        $this->infinityLoop = true;
    }

    public function getFiles(): array
    {
        $files = [];
        if ($this->infinityLoop) {
            $files[] = new File('Dockerfile', $this->getDockerfileContent(), $this->fs);
        }
        return $files;
    }

    private function getDockerfileContent(): string
    {
        $imageTag = $this->versionMapping[$this->phpVersion] ?? $this->phpVersion;

        $recipeLines = [
            "FROM php:{$imageTag}",
            "",
        ];

        if ($this->composer) {
            $recipeLines[] = "RUN php -r \"copy('https://getcomposer.org/installer', 'composer-setup.php');\"";
            $recipeLines[] = "RUN php composer-setup.php --install-dir=/usr/local/bin --filename=composer";
            $recipeLines[] = "";
        }

        $recipeLines[] = "CMD while : ; do sleep 1000; done";

        return implode("\n", $recipeLines);
    }

    protected function buildYamlStructure(): void
    {
        $imageTag = $this->versionMapping[$this->phpVersion] ?? $this->phpVersion;
        
        $this->yamlStructure = [
            $this->name => []
        ];

        if ($this->infinityLoop) {
            $this->yamlStructure[$this->name]['build'] = [
                'context' => '.'
            ];
        } else {
            $this->yamlStructure[$this->name]['image'] = 'php:' . $imageTag;
        }

        $this->yamlStructure[$this->name]['container_name'] = $this->name;

        if ($this->networkModeHost) {
            $this->yamlStructure[$this->name]['network_mode'] = 'host';
        }
    }
}
