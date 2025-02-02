<?php

declare(strict_types=1);

namespace App\Tests\ReceiptApp\Receipts\Questions;

use App\ReceiptApp\Receipts\Questions\PhpDevMysqlQuestions;
use App\ReceiptApp\Receipts\Questions\Types\QuestionEntry;
use PHPUnit\Framework\TestCase;

class PhpDevMysqlQuestionsTest extends TestCase
{
    public function testQuestionsEntries(): void
    {
        $questions = new PhpDevMysqlQuestions();

        foreach ($questions->getPropertyQuestionPair() as $questionEntry) {
            $this->assertInstanceOf(expected: QuestionEntry::class, actual: $questionEntry);
        }
    }
}
