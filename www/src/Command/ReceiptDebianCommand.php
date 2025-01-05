<?php

declare(strict_types=1);

namespace App\Command;

use App\ReceiptApp\Receipts\Debian;
use App\ReceiptApp\Traits\PrepareExecution;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Question\ConfirmationQuestion;

#[AsCommand(
    name: 'receipt:debian',
    description: 'Generate the most simple debian container',
)]
class ReceiptDebianCommand extends Command
{
    use PrepareExecution;

    private Debian $receipt;

    public function __construct()
    {
        parent::__construct();
        $this->fs = new Filesystem();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->prepareExecution($input, $output, new Debian());
        
        $io = new SymfonyStyle($input, $output);

        $dirPath = $this->getDirPath();

        foreach ($this->receipt->getPropertyQuestionsPairs() as $propertyQuestionPair) {
            $this->feedReceipt($propertyQuestionPair);    
        }

        $questionApp = new ConfirmationQuestion(
            "Should this receipt be hosted in /app? Type yes or y for yes, or no or n for no. Default is no. \n", 
            false
        );

        $this->makerFile($dirPath,$this->receipt);
        
        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
