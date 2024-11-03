<?php

namespace App\ReceiptApp\Receipts\Questions;

class DebianQuestion implements QuestionInterface
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
