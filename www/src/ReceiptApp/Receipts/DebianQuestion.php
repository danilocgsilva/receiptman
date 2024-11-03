<?php

namespace App\ReceiptApp\Receipts;

class DebianQuestion
{
    private array $propertyQuestionPair;

    public function __construct()
    {
        $this->propertyQuestionPair = [
            ["setName", "Write the container name\n"]
        ];
    }

    public function getPropertyQuestionPair(): array
    {
        return $this->propertyQuestionPair;
    }
}
