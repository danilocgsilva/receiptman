<?php

namespace App\Command;

use App\Command\Traits\ReceiptFolder;
use App\ReceiptApp\Receipts\PythonReceipt;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use App\ReceiptApp\Traits\PrepareExecution;

#[AsCommand(
    name: 'receipt:python',
    description: 'Receipt with python.',
)]
class PythonCommand extends ReceiptmanCommand
{
    use PrepareExecution;
    use ReceiptFolder;

    private $input;

    private $output;

    private $questionHelper;

    private PythonReceipt $receipt;

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->prepareExecution($input, $output, new PythonReceipt());

        $io = new SymfonyStyle($input, $output);

        foreach ($this->receipt->getPropertyQuestionsPairs() as $propertyQuestionPair) {
            $this->feedReceipt($propertyQuestionPair);    
        }

        $dirPath = $this->askForReceiptFolderAndWriteFiles();

        $io->success(sprintf("Project created in %1\$s.", $dirPath));

        return Command::SUCCESS;
    }
}
