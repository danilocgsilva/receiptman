<?php

namespace App\ReceiptApp\Receipts\Questions;

class BaseQuestion
{
    protected array $propertyQuestionPair;

    public function __construct()
    {
        $this->propertyQuestionPair = [
            ["setName", "Write the container name\n", null],
            ["setNetworkModeHost", "Should the container uses the host network?\n", "yesorno"]
        ];
    }

    public function getPropertyQuestionPair(): array
    {
        return $this->propertyQuestionPair;
    }
}
