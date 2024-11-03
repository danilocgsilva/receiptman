<?php

namespace App\ReceiptApp\Receipts\Questions;

interface QuestionInterface
{
    public function getPropertyQuestionPair(): array;
}