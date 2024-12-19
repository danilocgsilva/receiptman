<?php

declare(strict_types=1);

namespace App\ReceiptApp\Receipts\Questions;

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
            ]
        );
    }
}
