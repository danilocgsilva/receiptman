<?php

declare(strict_types=1);

namespace App\Utilities;

use App\ReceiptApp\Receipts\Interfaces\ReceiptInterface;

class WrapServicesYamlStructure
{
    /** @var \App\ReceiptApp\Receipts\Interfaces\ReceiptInterface[] $receipts */
    private array $receipts;
    
    public function __construct(ReceiptInterface ...$receipts)
    {
        $this->receipts = $receipts;
    }

    public function getFullDockerComposeYamlStructure(): array
    {
        $yamlStructure = $this->receipts[0];
        
        return [
            'services' => $yamlStructure->getServiceYamlStructure()
        ];
    }
}