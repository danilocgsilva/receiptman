<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\{
    Attribute\AsCommand,
    Command\Command,
    Style\SymfonyStyle
};
use Symfony\Component\Console\{
    Input\InputInterface,
    Output\OutputInterface
};
use App\ReceiptApp\Receipts\DotNetReceipt;
use App\ReceiptApp\Traits\PrepareExecution;
use App\Command\Traits\ReceiptFolder;
use Symfony\Component\Filesystem\Filesystem;

#[AsCommand(
    name: 'receipt:dotnet',
    description: '.NET server',
)]
class DotNetCommand extends ReceiptmanCommand
{
    use PrepareExecution;
    use ReceiptFolder;

    protected Filesystem $fs;
    
    private $input;

    private $output;

    private $questionHelper;

    private DotNetReceipt $receipt;

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->prepareExecution($input, $output, new DotNetReceipt($this->fs));
        $io = new SymfonyStyle($input, $output);

        while ($propertyQuestionPair = $this->receipt->getNextQuestionPair()) {
            $this->feedReceipt($propertyQuestionPair);
        }

        $dirPath = $this->askForReceiptFolderAndWriteFiles();

        $io->success(sprintf("Project created in %1\$s.", $dirPath));

        return Command::SUCCESS;
    }
}
