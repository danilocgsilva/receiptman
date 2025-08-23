<?php

declare(strict_types=1);

namespace App\ReceiptApp\Receipts\Questions;

use App\ReceiptApp\Receipts\Questions\Types\QuestionEntry;
use App\ReceiptApp\Receipts\Questions\Types\InputType;

class PythonQuestion extends BaseQuestion implements QuestionInterface
{
    public function __construct()
    {
        parent::__construct();

        $this->propertyQuestionPair = array_merge(
            $this->propertyQuestionPair,
            [
                new QuestionEntry(
                    methodName: "setPip",
                    textQuestion: "Install pip?\n",
                    inputType: InputType::yesorno
                )
                ],
                            [
                new QuestionEntry(
                    methodName: "setInstallGit",
                    textQuestion: "Install git in environment?\n",
                    inputType: InputType::yesorno
                )
            ]
        );
    }
}
