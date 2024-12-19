<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\{
    Input\InputInterface,
    Output\OutputInterface
};
use Symfony\Component\Console\Command\Command;
use App\ReceiptApp\Traits\PrepareExecution;
use App\ReceiptApp\Receipts\Apache as ApacheReceipt;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Command\Traits\ReceiptFolder;

#[AsCommand(
    name: 'receipt:apache',
    description: 'Apache server',
)]
class Apache extends ReceiptmanCommand
{
    use PrepareExecution;
    use ReceiptFolder;

    private ApacheReceipt $receipt;

    private $questionHelper;

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->prepareExecution($input, $output, new ApacheReceipt());

        $io = new SymfonyStyle($input, $output);

        while ($propertyQuestionPair = $this->receipt->getNextQuestionPair()) {
            $this->feedReceipt($propertyQuestionPair);
        }

        $dirPath = $this->askForReceiptFolder();

        $io->success(sprintf("Project created in %1\$s.", $dirPath));

        return Command::SUCCESS;
    }
}
