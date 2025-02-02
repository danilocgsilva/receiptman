<?php

declare(strict_types=1);

namespace App\Tests\Traits;

trait HasQuestionWithMethod
{
    /**
     * @param string $methodName
     * @param \App\ReceiptApp\Receipts\Questions\Types\QuestionEntry[] $questions
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
