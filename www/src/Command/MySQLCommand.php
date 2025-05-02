<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\ReceiptApp\Traits\PrepareExecution;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use App\Command\Traits\ReceiptFolder;
use App\ReceiptApp\Receipts\MySQLReceipt;

#[AsCommand(
    name: 'receipt:mysql',
    description: 'Mysql database receipt',
)]
class MySQLCommand extends ReceiptmanCommand
{
    use PrepareExecution;
    use ReceiptFolder;

    protected Filesystem $fs;

    private MySQLReceipt $receipt;

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->prepareExecution($input, $output, new MySQLReceipt($this->fs));

        $io = new SymfonyStyle($input, $output);

        foreach ($this->receipt->getPropertyQuestionsPairs() as $propertyQuestionPair) {
            $this->feedReceipt($propertyQuestionPair);    
        }

        $dirPath = $this->askForReceiptFolderAndWriteFiles();

        $io->success(sprintf("Project created in %1\$s.", $dirPath));

        return Command::SUCCESS;
    }
}