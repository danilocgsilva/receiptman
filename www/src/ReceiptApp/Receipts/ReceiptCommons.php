<?php

declare(strict_types=1);

namespace App\ReceiptApp\Receipts;

use App\ReceiptApp\Receipts\Questions\QuestionEntry;
use App\ReceiptApp\Receipts\Interfaces\HttpReportableInterface;

class ReceiptCommons
{
    protected bool $sshVolume = false;

    protected array $yamlStructure;

    protected bool $networkModeHost = false;

    protected string $name;

    protected int $currentQuestion = 0;

    /**
     * @var \App\ReceiptApp\Receipts\QuestionInterface[];
     */
    protected array $questionsPairs;

    // public function __construct()
    // {
    //     $this->currentQuestion = 0;
    // }
    
    public function setSshVolume(): self
    {
        $this->sshVolume = true;
        return $this;
    }

    /**
     * Set a name for contrainer
     * @param string $name
     * @return ReceiptCommons
     */
    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return \App\ReceiptApp\Receipts\QuestionInterface[]
     */
    public function getPropertyQuestionsPairs(): array
    {
        return $this->questionsPairs;
    }
    public function setNetworkModeHost(): self
    {
        $this->networkModeHost = true;
        return $this;
    }

    /**
     * @return \App\ReceiptApp\Receipts\Questions\QuestionEntry|null
     */
    public function getNextQuestionPair(): QuestionEntry|null
    {
        return $this->questionsPairs[$this->currentQuestion++] ?? null;
    }

    protected function postYamlProcessing(): void
    {
        if ($this->sshVolume) {
            $this->yamlStructure['services'][$this->name]['volumes'][] = './.ssh/:/root/.ssh';
        }
    }
}
