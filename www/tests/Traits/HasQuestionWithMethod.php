<?php

namespace App\Tests\Traits;

use App\ReceiptApp\File;
use App\ReceiptApp\Receipts\Questions\QuestionEntry;

trait HasQuestionWithMethod
{
    /**
     * @param string $methodName
     * @param \App\ReceiptApp\Receipts\Questions\QuestionEntry[] $questions
     * @return bool
     */
    private function hasQuestionWithMethod(string $methodName, array $questions): bool
    {
        foreach ($questions as $question) {
            if ($question->methodName === $methodName) {
                return true;
            }
        }
        return false;
    }
}
