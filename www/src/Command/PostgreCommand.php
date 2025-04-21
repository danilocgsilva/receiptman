<?php

declare(strict_types=1);

namespace App\Command;

use App\Command\Traits\ReceiptFolder;
use App\ReceiptApp\Receipts\PostgreReceipt;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\ReceiptApp\Traits\PrepareExecution;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'receipt:postgre',
    description: 'Postgre environment',
)]
class PostgreCommand extends ReceiptmanCommand
{
    use PrepareExecution;
    use ReceiptFolder;

    private $input;

    private $output;

    private $questionHelper;

    private PostgreReceipt $receipt;

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->prepareExecution($input, $output, new PostgreReceipt());

        $io = new SymfonyStyle($input, $output);

        while ($propertyQuestionPair = $this->receipt->getNextQuestionPair()) {
            $this->feedReceipt($propertyQuestionPair);
        }

        $dirPath = $this->askForReceiptFolderAndWriteFiles();

        $io->success(sprintf("Project created in %1\$s.", $dirPath));

        return Command::SUCCESS;
    }
}