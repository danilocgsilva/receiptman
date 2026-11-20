<?php

declare(strict_types=1);

namespace App\ReceiptApp\Receipts\Questions;

use App\ReceiptApp\Receipts\Interfaces\QuestionInterface;

use App\ReceiptApp\Receipts\Questions\Types\InputType;
use App\ReceiptApp\Receipts\Questions\Types\QuestionEntry;

class BaseQuestion implements QuestionInterface
{
    /** @var QuestionEntry[] */
    protected array $propertyQuestionPair;

    public function __construct(private readonly string $containerLabel = 'the container')
    {
        $this->propertyQuestionPair = [
            new QuestionEntry(
                methodName: "setName",
                textQuestion: "Write {$this->containerLabel} name\n"
            ),
            new QuestionEntry(
                methodName: "setNetworkModeHost",
                textQuestion: "Should {$this->containerLabel} use the host network?\n",
                inputType: InputType::yesorno
            ),
            new QuestionEntry(
                methodName: "setSshVolume",
                textQuestion: "Should I mount the .ssh in a local volume for {$this->containerLabel}?\n",
                inputType: InputType::yesorno
            ),
            new QuestionEntry(
                methodName: "setWindowsExtraHost",
                textQuestion: "Should I add the Windows extra host (host.docker.internal) for {$this->containerLabel}?\n",
                inputType: InputType::yesorno
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
