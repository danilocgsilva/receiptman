<?php

declare(strict_types=1);

namespace App\ReceiptApp\Receipts;

class ReceiptCommons
{
    protected bool $sshVolume = false;

    protected array $yamlStructure;

    protected bool $networkModeHost = false;

    protected string $name;
    
    public function setSshVolume(): self
    {
        $this->sshVolume = true;
        return $this;
    }

    public function setNetworkModeHost(): self
    {
        $this->networkModeHost = true;
        return $this;
    }

    protected function postYamlProcessing(): void
    {
        if ($this->sshVolume) {
            $this->yamlStructure['services'][$this->name]['volumes'][] = './.ssh/:/root/.ssh';
        }
    }
}
