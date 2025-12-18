<?php

declare(strict_types=1);

namespace App\Command;

use App\ReceiptApp\Receipts\DebianReceipt;
use App\ReceiptApp\Traits\PrepareExecution;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Command\Traits\ReceiptFolder;
use Symfony\Component\Filesystem\Filesystem;

#[AsCommand(
    name: 'receipt:debian',
    description: 'Generate the most simple debian container',
)]
class DebianCommand extends ReceiptmanCommand
{
    use PrepareExecution;
    use ReceiptFolder;

    private $input;

    protected Filesystem $fs;

    private $output;

    private $questionHelper;

    private DebianReceipt $receipt;

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->prepareExecution($input, $output, new DebianReceipt($this->fs));
        
        $io = new SymfonyStyle($input, $output);

        foreach ($this->receipt->getPropertyQuestionsPairs() as $propertyQuestionPair) {
            $this->feedReceipt($propertyQuestionPair);    
        }

        $dirPath = $this->askForReceiptFolderAndWriteFiles();

        $io->success(sprintf("Project created in %1\$s.", $dirPath));

        return Command::SUCCESS;
    }
}
