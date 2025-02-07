<?php

declare(strict_types=1);

namespace App\ReceiptApp\Receipts\Questions;

use App\ReceiptApp\Receipts\Questions\Types\InputType;
use App\ReceiptApp\Receipts\Questions\Types\QuestionEntry;

class PostgreQuestions extends BaseQuestion implements QuestionInterface
{
    public function __construct()
    {
        parent::__construct();
    }
}