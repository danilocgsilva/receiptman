<?php

namespace App\ReceiptApp\Receipts\Questions;

class NodeQuestion extends BaseQuestion implements QuestionInterface
{
    public function __construct()
    {
        $this->propertyQuestionPair = [
            ["setName", "Write the container name\n", null]
        ];
    }
}
