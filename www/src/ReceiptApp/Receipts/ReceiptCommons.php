<?php

declare(strict_types=1);

namespace App\ReceiptApp\Receipts;

use App\ReceiptApp\Receipts\Questions\Types\QuestionEntry;
use Symfony\Component\Filesystem\Filesystem;

class ReceiptCommons
{
    protected bool $sshVolume = false;

    protected array $yamlStructure;

    protected bool $networkModeHost = false;

    protected string $name;

    protected int $currentQuestion = 0;
    
    /**
     * @var \App\ReceiptApp\Receipts\Questions\Types\QuestionEntry[]
     */
    protected array $questionsPairs;

    public function __construct(protected Filesystem $fs)
    {
    }

    public function setSshVolume(): self
    {
        $this->sshVolume = true;
        return $this;
    }

    /**
     * Set a name for contrainer
     * @param string $name
     * @return static
     */
    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return \App\ReceiptApp\Receipts\Questions\Types\QuestionEntry[]
     */
    public function getPropertyQuestionsPairs(): array
    {
        return $this->questionsPairs;
    }

    public function setNetworkModeHost(): self
    {
        $this->networkModeHost = true;
        
        $arrayPosition = array_search(
            "setHttpPortRedirection", 
            array_map(fn (QuestionEntry $questionEntry) => $questionEntry->methodName, $this->questionsPairs),
             true
        );
        unset($this->questionsPairs[$arrayPosition]);
        $this->questionsPairs = array_values($this->questionsPairs);

        return $this;
    }

    /**
     * @return \App\ReceiptApp\Receipts\Questions\Types\QuestionEntry|null
     */
    public function getNextQuestionPair(): QuestionEntry|null
    {
        return $this->questionsPairs[$this->currentQuestion++] ?? null;
    }

    public function getServiceYamlStructure(): array
    {
        if (empty($this->yamlStructure) && method_exists($this, 'buildYamlStructure')) {
            call_user_func([$this, 'buildYamlStructure']);
        }
        return $this->yamlStructure;
    }

    /**
     * For specific receipt files, a override must be made.
     */
    public function getFiles(): array
    {
        return [];
    }

    protected function postYamlProcessing(): void
    {
        if ($this->sshVolume) {
            $this->yamlStructure[$this->name]['volumes'][] = './.ssh/:/root/.ssh';
        }
    }
}
