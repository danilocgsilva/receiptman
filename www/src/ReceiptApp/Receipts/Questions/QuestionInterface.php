<?php

declare(strict_types=1);

namespace App\ReceiptApp\Receipts\Questions;

interface QuestionInterface
{
    public function getPropertyQuestionPair(): array;
}