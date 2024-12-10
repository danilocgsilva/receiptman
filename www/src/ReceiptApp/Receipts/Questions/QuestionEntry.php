<?php

declare(strict_types=1);

namespace App\ReceiptApp\Receipts\Questions;

class QuestionEntry
{
    public function __construct(
        public readonly string $methodName,
        public readonly string $textQuestion,
        public readonly ?string $inputType = null
    ) {}
}
