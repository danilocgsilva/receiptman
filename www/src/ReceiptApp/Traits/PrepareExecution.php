<?php

namespace App\ReceiptApp\Traits;

use App\ReceiptApp\Receipts\PhpDevMysql;
use App\ReceiptApp\Receipts\ReceiptInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use DateTime;

trait PrepareExecution
{
    private function prepareExecution(
        InputInterface $input, 
        OutputInterface $output, 
        ReceiptInterface $receipt
    ): void
    {
        $this->input = $input;
        $this->output = $output;
        $this->questionHelper = $this->getHelper('question');
        $this->receipt = $receipt;
    }

    private function getBaseDateString(): string
    {
        return (new DateTime())->format('Ymd-his');
    }

    private function getDirPath(): string
    {
        $baseFolderName = $this->getBaseDateString();
        return 'output' . DIRECTORY_SEPARATOR . $baseFolderName;
    }
}