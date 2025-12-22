<?php

declare(strict_types=1);

namespace App\ReceiptApp\Receipts;

use App\ReceiptApp\Receipts\Interfaces\PhpInterface;
use App\ReceiptApp\Receipts\Interfaces\ReceiptInterface;
use App\ReceiptApp\Receipts\Questions\BaseQuestion;
use Symfony\Component\Filesystem\Filesystem;
use App\ReceiptApp\Receipts\Questions\Types\QuestionEntry;

class PhpReceipt extends ReceiptCommons implements ReceiptInterface, PhpInterface
{
    private string $phpVersion = "8.2";
    
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

    public function setPhpVersion(string $phpVersion): static
    {
        $this->phpVersion = $phpVersion;
        return $this;
    }

    protected function buildYamlStructure(): void
    {
        $this->yamlStructure = [
            $this->name => [
                'image' => 'php:latest',
                'container_name' => $this->name
            ]
        ];

        if ($this->networkModeHost) {
            $this->yamlStructure[$this->name]['network_mode'] = 'host';
        }
    }
}
