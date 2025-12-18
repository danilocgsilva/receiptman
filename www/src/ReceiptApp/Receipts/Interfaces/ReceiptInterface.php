<?php

declare(strict_types=1);

namespace App\ReceiptApp\Receipts\Interfaces;

use App\ReceiptApp\Receipts\Questions\Types\QuestionEntry;

/**
 * @template T
 */
interface ReceiptInterface
{
    /** @return \App\ReceiptApp\File[] */
    public function getFiles(): array;

    public function getPropertyQuestionsPairs(): array;

    public function setName(string $name): static;

    public function getNextQuestionPair(): QuestionEntry|null;

    public function getServiceYamlStructure(): array;
}
