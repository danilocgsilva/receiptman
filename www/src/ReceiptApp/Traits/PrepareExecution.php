<?php

namespace App\ReceiptApp\Traits;

use App\ReceiptApp\Receipts\PhpDevMysql;
use App\ReceiptApp\Receipts\ReceiptInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use DateTime;
use Symfony\Component\Console\Question\Question;
use Exception;

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

    private function feedReceipt(string $receiptSetterName, string $questionString)
    {
        $this->receipt->{$receiptSetterName}(
            $this->makeQuestionAndGetAnswer($questionString)
        );
    }

    private function makeQuestionAndGetAnswer(string $questionTitle): string
    {
        $question = new Question($questionTitle);
        return (string) $this->questionHelper->ask($this->input, $this->output, $question);
    }

    private function makerFile(string $dirPath, ReceiptInterface $receipt)
    {
        if ($this->fs->exists($dirPath)) {
            throw new Exception(sprintf("The path %1\$s exists. Action aborted with exception.", $dirPath));
        }
        $this->fs->mkdir($dirPath);

        $files = $receipt->getFiles();
        foreach ($files as $file) {
            $file->write($dirPath, $this->fs);
        }
    }
}