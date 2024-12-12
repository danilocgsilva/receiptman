<?php

declare(strict_types=1);

namespace App\Tests\ReceiptApp\Receipts\Questions;

use App\ReceiptApp\Receipts\Questions\QuestionEntry;
use PHPUnit\Framework\TestCase;
use App\ReceiptApp\Receipts\Questions\NginxQuestions;

class NginxQuestionsTest extends TestCase
{
    public function testQuestionsEntries(): void
    {
        $questions = new NginxQuestions();

        foreach ($questions->getPropertyQuestionPair() as $questionEntry) {
            $this->assertInstanceOf(expected: QuestionEntry::class, actual: $questionEntry);
        }
    }
}

