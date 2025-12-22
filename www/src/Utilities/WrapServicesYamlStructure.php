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
        $services = [];

        foreach ($this->receipts as $receipt) {
            $structure = $receipt->getServiceYamlStructure();
            if (!is_array($structure)) {
                continue;
            }
            $services = array_merge($services, $structure);
        }

        return [
            'services' => $services
        ];
    }
}