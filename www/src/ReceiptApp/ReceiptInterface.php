<?php

namespace App\ReceiptApp;

interface ReceiptInterface
{
    /**
     * @return \App\ReceiptApp\File[]
     */
    public function getFiles(): array;
}
