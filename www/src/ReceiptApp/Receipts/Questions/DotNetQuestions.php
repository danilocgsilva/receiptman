<?php

declare(strict_types=1);

namespace App\ReceiptApp\Receipts\Questions;

use App\ReceiptApp\Receipts\Questions\Types\InputType;
use App\ReceiptApp\Receipts\Questions\Types\QuestionEntry;

class DotNetQuestions extends BaseQuestion implements QuestionInterface
{
    public function __construct()
    {
        parent::__construct();

        $this->propertyQuestionPair = array_merge(
            $this->propertyQuestionPair,
            [
                new QuestionEntry(
                    "setHostMountVolume",
                    "Should the receipt provides a mounted volume in the host?\n",
                    InputType::yesorno
                )
            ],
            [
                new QuestionEntry(
                    "setDatabase",
                    "Should the receipt have a database?\n",
                    InputType::yesorno
                )
            ]
        );
    }
}
