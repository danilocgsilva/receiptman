<?php

declare(strict_types=1);

namespace App\ReceiptApp\Receipts\Questions\Types;

use App\ReceiptApp\Receipts\Questions\Types\InputType;

/**
 * Question entry is responsible to hold the question title (what the receipt
 *   needs), the receipt method name to set the answer, and the input type
 *   (yes/no or standard input).
 */
class QuestionEntry
{
    public function __construct(
        public readonly string $methodName,
        public readonly string $textQuestion,
        public readonly ?InputType $inputType = null
    ) {}
}
