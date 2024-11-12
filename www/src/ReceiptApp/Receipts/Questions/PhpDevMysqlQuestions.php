<?php

namespace App\ReceiptApp\Receipts\Questions;

class PhpDevMysqlQuestions extends BaseQuestion implements QuestionInterface
{
    public function __construct()
    {
        parent::__construct();

        $this->propertyQuestionPair = array_merge(
            $this->propertyQuestionPair,
            [["setHttpPortRedirection", "Write the port number redirection for http\n", null]],
            [["setMysqlPortRedirection", "Write the port number redirection for mysql\n", null]],
            [["setMysqlRootPassword", "Write the mysql root password\n", null]]
        );
    }
}
