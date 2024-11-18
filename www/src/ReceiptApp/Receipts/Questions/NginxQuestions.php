<?php

declare(strict_types=1);

namespace App\ReceiptApp\Receipts\Questions;

class NginxQuestions extends BaseQuestion implements QuestionInterface
{
    public function __construct()
    {
        parent::__construct();

        $this->propertyQuestionPair = array_merge(
            $this->propertyQuestionPair,
            [["setHttpPortRedirection", "Write the port number redirection for http\n", null]],
            [["onExposeDefaultServerFile", "Should the default server configuration be exposed?\n", "yesorno"]]
        );
    }
}
