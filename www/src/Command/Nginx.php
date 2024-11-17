<?php

declare(strict_types=1);

namespace App\Command;

use App\Command\Traits\ReceiptFolder;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use App\ReceiptApp\Traits\PrepareExecution;
use App\ReceiptApp\Receipts\NginxReceipt;
use Symfony\Component\Console\Question\ConfirmationQuestion;

#[AsCommand(
    name: 'receipt:nginx',
    description: 'Nginx server',
)]
class Nginx extends Command
{
    use PrepareExecution;
    use ReceiptFolder;

    private $input;

    private $output;

    private Filesystem $fs;

    private NginxReceipt $receipt;

    public function __construct()
    {
        parent::__construct();
        $this->fs = new Filesystem();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->prepareExecution($input, $output, new NginxReceipt());

        $io = new SymfonyStyle($input, $output);

        foreach ($this->receipt->getPropertyQuestionsPairs() as $propertyQuestionPair) {
            $this->feedReceipt($propertyQuestionPair[0], $propertyQuestionPair[1], $propertyQuestionPair[2]);    
        }

        $dirPath = $this->askForReceiptFolder();

        $io->success(sprintf("Project created in %1\$s.", $dirPath));

        return Command::SUCCESS;
    }
}
