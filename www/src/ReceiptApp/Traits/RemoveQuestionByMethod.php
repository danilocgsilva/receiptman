<?php

declare(strict_types= 1);

namespace App\ReceiptApp\Traits;

use App\ReceiptApp\Receipts\Questions\QuestionEntry;

trait RemoveQuestionByMethod
{
    /**
     * @param string $method
     * @param \App\ReceiptApp\Receipts\Questions\QuestionEntry[] $question
     * @return bool
     */
    private function removeQuestionByMethod(string $method, array &$questions): bool
    {
        foreach ($questions as $key => $question) {
            if ($question->methodName === $method) {
                unset($questions[$key]);
                array_values($questions);
                return true;
            }
        }
        return false;
    }
}
