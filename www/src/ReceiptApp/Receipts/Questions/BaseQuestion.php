<?php

declare(strict_types=1);

namespace App\ReceiptApp\Receipts\Questions;

class BaseQuestion
{
    protected array $propertyQuestionPair;

    public function __construct()
    {
        $this->propertyQuestionPair = [
            ["setName", "Write the container name\n", null],
            ["setNetworkModeHost", "Should the container uses the host network?\n", "yesorno"],
            ["setSshVolume", "Should I mount the .ssh in a local volume?\n", "yesorno"]
        ];
    }

    public function getPropertyQuestionPair(): array
    {
        return $this->propertyQuestionPair;
    }
}
