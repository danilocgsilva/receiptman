<?php

declare(strict_types=1);

namespace App\ReceiptApp\Receipts\Questions;

use App\ReceiptApp\Receipts\Questions\InputType;

class NodeQuestion extends BaseQuestion implements QuestionInterface
{
    public function __construct()
    {
        parent::__construct();

        $this->propertyQuestionPair = array_merge(
            $this->propertyQuestionPair,
            [
                new QuestionEntry(
                    methodName: "setVolumeApp",
                    textQuestion: "Should this receipt have a app folder in volume?\n",
                    inputType: InputType::yesorno
                )
            ]
        );
    }
}
