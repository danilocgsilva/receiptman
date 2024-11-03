<?php

namespace App\ReceiptApp\Receipts\Questions;

class BaseQuestion
{
    protected array $propertyQuestionPair;

    public function getPropertyQuestionPair(): array
    {
        return $this->propertyQuestionPair;
    }
}
