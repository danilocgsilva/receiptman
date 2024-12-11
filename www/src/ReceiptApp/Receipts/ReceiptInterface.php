<?php

declare(strict_types=1);

namespace App\ReceiptApp\Receipts;

/**
 * @template T
 */
interface ReceiptInterface
{
    /**
     * @return \App\ReceiptApp\File[]
     */
    public function getFiles(): array;

    public function getPropertyQuestionsPairs(): array;

    public function setName(string $name): static;
}
