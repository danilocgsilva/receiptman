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
use Symfony\Component\Filesystem\Filesystem;
use App\ReceiptApp\Receipts\NginxReceipt;

#[AsCommand(
    name: 'receipt:nginx',
    description: 'Nginx server',
)]
class Nginx extends ReceiptmanCommand
{
    use PrepareExecution;
    use ReceiptFolder;

    protected Filesystem $fs;

    private $input;

    private $output;

    private NginxReceipt $receipt;

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->prepareExecution($input, $output, new NginxReceipt());

        $io = new SymfonyStyle($input, $output);

        foreach ($this->receipt->getPropertyQuestionsPairs() as $propertyQuestionPair) {
            $this->feedReceipt($propertyQuestionPair);    
        }

        $dirPath = $this->askForReceiptFolderAndWriteFiles();

        $io->success(sprintf("Project created in %1\$s.", $dirPath));

        return Command::SUCCESS;
    }
}
