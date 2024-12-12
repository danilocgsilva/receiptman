<?php

declare(strict_types=1);

namespace App\Tests\ReceiptApp\Receipts\Questions;

use App\ReceiptApp\Receipts\Questions\QuestionEntry;
use PHPUnit\Framework\TestCase;
use App\ReceiptApp\Receipts\Questions\ApacheQuestions;

class ApacheQuestionsTest extends TestCase
{
    public function testQuestionsEntries(): void
    {
        $questions = new ApacheQuestions();

        foreach ($questions->getPropertyQuestionPair() as $questionEntry) {
            $this->assertInstanceOf(expected: QuestionEntry::class, actual: $questionEntry);
        }
    }
}

