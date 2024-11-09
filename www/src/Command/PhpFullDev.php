<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use App\ReceiptApp\Receipts\PhpDevMysql;
use App\ReceiptApp\Traits\PrepareExecution;
use Symfony\Component\Console\Question\ConfirmationQuestion;

#[AsCommand(
    name: 'receipt:php-full-dev',
    description: 'Receipt with PHP with xdebug, Apache and MySQL',
)]
class PhpFullDev extends Command
{
    use PrepareExecution;

    private Filesystem $fs;

    private $input;

    private $output;

    private $questionHelper;

    private PhpDevMysql $receipt;
    
    public function __construct()
    {
        parent::__construct();
        $this->fs = new Filesystem();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->prepareExecution($input, $output, new PhpDevMysql());

        $io = new SymfonyStyle($input, $output);

        $dirPath = $this->getDirPath();

        foreach ($this->receipt->getPropertyQuestionsPairs() as $propertyQuestionPair) {
            $this->feedReceipt($propertyQuestionPair[0], $propertyQuestionPair[1]);    
        }
        $questionApp = new ConfirmationQuestion("Should this receipt be hosted in /app? Type yes or y for yes, or no or n for no. \n", false);
        $responseQuestion = $this->questionHelper->ask($this->input, $this->output, $questionApp);
        if ($responseQuestion) {
            $this->receipt->setAppFolder();
        }

        $this->makerFile($dirPath,$this->receipt);

        $io->success(sprintf("Project created in %1\$s.", $dirPath));

        return Command::SUCCESS;
    }
}
