<?php

declare(strict_types=1);

namespace App\ReceiptApp\Receipts;

interface ReceiptInterface
{
    /**
     * @return \App\ReceiptApp\File[]
     */
    public function getFiles(): array;

    public function getPropertyQuestionsPairs(): array;
}
