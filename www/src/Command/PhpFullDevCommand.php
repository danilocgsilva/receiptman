<?php

namespace App\Command;

use App\Command\Traits\ReceiptFolder;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\ReceiptApp\Receipts\PhpFullDevReceipt;
use App\ReceiptApp\Traits\PrepareExecution;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Filesystem\Filesystem;

#[AsCommand(
    name: 'receipt:php-full-dev',
    description: 'Receipt with PHP with xdebug, Apache and MySQL',
)]
class PhpFullDevCommand extends ReceiptmanCommand
{
    use PrepareExecution;
    use ReceiptFolder;

    protected Filesystem $fs;

    private $input;

    private $output;

    private $questionHelper;

    private PhpFullDevReceipt $receipt;

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->prepareExecution($input, $output, new PhpFullDevReceipt($this->fs));

        $io = new SymfonyStyle($input, $output);

        $questionNeedsDb = new ConfirmationQuestion("Should the receipt have a database? \n", true);
        if (!$this->questionHelper->ask($this->input, $this->output, $questionNeedsDb)) {
            $this->receipt->setNoDatabase();
        }

        while ($propertyQuestionPair = $this->receipt->getNextQuestionPair()) {
            $this->feedReceipt($propertyQuestionPair);
        }

        $questionApp = new ConfirmationQuestion(
            "Should this receipt be hosted in /app? Type yes or y for yes, or no or n for no. Default is no. \n", 
            false
        );

        $responseQuestion = $this->questionHelper->ask($this->input, $this->output, $questionApp);
        if ($responseQuestion) {
            $this->receipt->setAppFolder();
        }

        $dirPath = $this->askForReceiptFolderAndWriteFiles();

        $io->success(sprintf("Project created in %1\$s.", $dirPath));

        return Command::SUCCESS;
    }
}
