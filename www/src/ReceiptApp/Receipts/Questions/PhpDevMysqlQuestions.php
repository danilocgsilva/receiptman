<?php

declare(strict_types=1);

namespace App\ReceiptApp\Receipts\Questions;

class PhpDevMysqlQuestions extends BaseQuestion implements QuestionInterface
{
    public function __construct()
    {
        parent::__construct();

        $this->propertyQuestionPair = array_merge(
            $this->propertyQuestionPair,
            [
                new QuestionEntry(
                    methodName: "setHttpPortRedirection",
                    textQuestion: "Write the port number redirection for http\n"
                )
            ],
            [
                new QuestionEntry(
                    methodName: "setNoDatabase",
                    textQuestion: "Shoud the receipt have one receipt for relational database?\n",
                    inputType: "yesorno"
                )
            ],
            [
                new QuestionEntry(
                    methodName: "setPublicFolderAsHost",
                    textQuestion: "Should the environment root folder have the name \"public\"? (Currently, it is \"html\")\n",
                    inputType: "yesorno"
                )
            ],
            [
                new QuestionEntry(
                    methodName: "setMysqlPortRedirection",
                    textQuestion: "Write the port number redirection for mysql\n"
                )
            ],
            [
                new QuestionEntry(
                    methodName: "setMysqlRootPassword",
                    textQuestion: "Write the mysql root password\n"
                )
            ]
        );
    }
}
