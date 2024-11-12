<?php

namespace App\ReceiptApp\Receipts\Questions;

class NodeQuestion extends BaseQuestion implements QuestionInterface
{
    public function __construct()
    {
        parent::__construct();
        
        $this->propertyQuestionPair = array_merge(
            $this->propertyQuestionPair,
            [["setVolumeApp", "Should this receipt have a app folder in volume?\n", "yesorno"]]
        );
    }
}
