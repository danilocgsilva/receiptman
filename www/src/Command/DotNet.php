<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\{
    Input\InputInterface,
    Output\OutputInterface
};
use App\ReceiptApp\Receipts\DotNet as DotNetReceipt;
use App\ReceiptApp\Traits\PrepareExecution;


#[AsCommand(
    name: 'receipt:dotnet',
    description: '.NET server',
)]
class DotNet extends Command
{
    use PrepareExecution;
    
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->prepareExecution($input, $output, new DotNetReceipt());

        return Command::SUCCESS;
    }
}
