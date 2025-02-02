<?php

declare(strict_types=1);

namespace App\ReceiptApp\Receipts\Questions;

use App\ReceiptApp\Receipts\Questions\Types\InputType;
use App\ReceiptApp\Receipts\Questions\Types\QuestionEntry;

class NginxQuestions extends BaseQuestion implements QuestionInterface
{
    public function __construct()
    {
        parent::__construct();

        $this->propertyQuestionPair = array_merge(
            $this->propertyQuestionPair,
            [
                new QuestionEntry(
                    "setHttpPortRedirection",
                    "Write the port number redirection for http\n"
                )
            ],
            [
                new QuestionEntry(
                    "onExposeDefaultServerFile",
                    "Should the default server configuration be exposed?\n",
                    InputType::yesorno
                )
            ]
        );
    }
}
