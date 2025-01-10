<?php

declare(strict_types=1);

namespace App\ReceiptApp\Receipts;

use App\ReceiptApp\Receipts\Interfaces\ReceiptInterface;
use App\ReceiptApp\File;
use Symfony\Component\Yaml\Yaml;

class REPLACEME1 extends ReceiptCommons implements ReceiptInterface
{
    /**
     * @inheritDoc
     */
    public function getFiles(): array
    {
        return $files;
    }

    private function buildYamlStructure(): void
    {
        $this->yamlStructure = [
            'services' => [
                $this->name => [
                    'image' => 'REPLACEME2',
                    'container_name' => $this->name
                ]
            ]
        ];
    }
}