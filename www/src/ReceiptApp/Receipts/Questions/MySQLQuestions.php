<?php

declare(strict_types=1);

namespace App\ReceiptApp\Receipts\Questions;

use App\ReceiptApp\Receipts\Questions\Types\InputType;
use App\ReceiptApp\Receipts\Questions\Types\QuestionEntry;

class MySQLQuestions extends BaseQuestion implements QuestionInterface
{
    public function __construct()
    {
        parent::__construct();

        $this->propertyQuestionPair = array_merge(
            $this->propertyQuestionPair,
            [
                new QuestionEntry(
                    methodName: "setMysqlPortRedirection",
                    textQuestion: "Write the port number redirection for mysql\n"
                ),
                new QuestionEntry(
                    methodName: "setMysqlRootPassword",
                    textQuestion: "Write the mysql root password\n"
                )
            ]
        );
    }
}