<?php

declare(strict_types=1);

namespace App\ReceiptApp\Receipts\Questions;

class ApacheQuestions extends BaseQuestion implements QuestionInterface
{
    public function __construct()
    {
        parent::__construct();

        $this->propertyQuestionPair = array_merge(
            $this->propertyQuestionPair,
            [new QuestionEntry("setHttpPortRedirection", "Write the port number redirection for http\n")]
        );
    }
}