<?php

declare(strict_types=1);

namespace App\ReceiptApp\Receipts\Questions;

use App\ReceiptApp\Receipts\Interfaces\QuestionInterface;

use App\ReceiptApp\Receipts\Questions\InputType;

class BaseQuestion implements QuestionInterface
{
    /** @var QuestionEntry[] */
    protected array $propertyQuestionPair;

    public function __construct()
    {
        $this->propertyQuestionPair = [
            new QuestionEntry(
                methodName: "setName",
                textQuestion: "Write the container name\n"
            ),
            new QuestionEntry(
                methodName: "setNetworkModeHost",
                textQuestion: "Should the container uses the host network?\n",
                inputType: InputType::yesorno
            ),
            new QuestionEntry(
                methodName: "setSshVolume",
                textQuestion: "Should I mount the .ssh in a local volume?\n"
            ),
        ];
    }

    /**
     * @return QuestionEntry[]
     */
    public function getPropertyQuestionPair(): array
    {
        return $this->propertyQuestionPair;
    }
}
