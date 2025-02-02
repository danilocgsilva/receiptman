<?php

declare(strict_types=1);

namespace App\ReceiptApp\Receipts\Questions\Types;

use App\ReceiptApp\Receipts\Questions\Types\InputType;

class QuestionEntry
{
    public function __construct(
        public readonly string $methodName,
        public readonly string $textQuestion,
        public readonly ?InputType $inputType = null
    ) {}
}
