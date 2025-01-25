<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\ReceiptApp\Traits\PrepareExecution;
use App\ReceiptApp\Receipts\PhpReceipt;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'receipt:php',
    description: 'Php receipt',
)]
class PhpCommand extends ReceiptmanCommand
{
    use PrepareExecution;

    private PhpReceipt $receipt;

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->prepareExecution($input, $output, new PhpReceipt());

        while ($propertyQuestionPair = $this->receipt->getNextQuestionPair()) {
            $this->feedReceipt($propertyQuestionPair);
        }

        $io = new SymfonyStyle($input, $output);

        return Command::SUCCESS;
    }
}
