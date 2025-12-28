<?php

declare(strict_types=1);

namespace App\Command;

use App\Command\Traits\ReceiptFolder;
use App\ReceiptApp\Traits\PrepareExecution;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\{
    Input\InputInterface,
    Output\OutputInterface
};
use Symfony\Component\Console\Style\SymfonyStyle;
use App\ReceiptApp\Receipts\NginxReceipt;
use Symfony\Component\Filesystem\Filesystem;

#[AsCommand(
    name: 'receipt:nginx',
    description: 'Nginx server',
)]
class NginxCommand extends ReceiptmanCommand
{
    use PrepareExecution;
    use ReceiptFolder;

    private $input;

    protected Filesystem $fs;

    private $output;

    private NginxReceipt $receipt;

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->prepareExecution($input, $output, new NginxReceipt($this->fs));

        $io = new SymfonyStyle($input, $output);

        foreach ($this->receipt->getPropertyQuestionsPairs() as $propertyQuestionPair) {
            $this->feedReceipt($propertyQuestionPair, $this->receipt);    
        }

        $dirPath = $this->askForReceiptFolderAndWriteFiles();

        $io->success(sprintf("Project created in %1\$s.", $dirPath));

        return Command::SUCCESS;
    }
}
