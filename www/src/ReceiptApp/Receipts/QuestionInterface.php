<?php

declare(strict_types=1);

namespace App\ReceiptApp\Receipts;

interface QuestionInterface
{
    public function getPropertyQuestionPair(): array;
}
