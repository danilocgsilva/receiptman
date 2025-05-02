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
use Symfony\Component\Filesystem\Filesystem;

#[AsCommand(
    name: 'receipt:mysql',
    description: 'Mysql database receipt',
)]
class MySQLCommand extends ReceiptmanCommand
{
    use PrepareExecution;

    protected Filesystem $fs;

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->prepareExecution($input, $output, new PhpReceipt($this->fs));

        $io = new SymfonyStyle($input, $output);

        return Command::SUCCESS;
    }
}