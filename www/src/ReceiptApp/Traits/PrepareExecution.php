<?php

namespace App\ReceiptApp\Traits;

use App\ReceiptApp\Receipts\PhpDevMysql;

trait PrepareExecution
{
    private function prepareExecution($input, $output): void
    {
        $this->input = $input;
        $this->output = $output;
        $this->questionHelper = $this->getHelper('question');
        $this->receipt = new PhpDevMysql();
    }
}