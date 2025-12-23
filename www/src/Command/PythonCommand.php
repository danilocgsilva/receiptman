<?php

namespace App\Command;

use App\Command\Traits\ReceiptFolder;
use App\ReceiptApp\Receipts\PythonReceipt;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use App\ReceiptApp\Traits\PrepareExecution;
use Symfony\Component\Console\Question\{Question, ConfirmationQuestion};

#[AsCommand(
    name: 'receipt:python',
    description: 'Receipt with python.',
)]
class PythonCommand extends ReceiptmanCommand
{
    use PrepareExecution;
    use ReceiptFolder;

    private $input;

    private $output;

    protected Filesystem $fs;

    private $questionHelper;

    private PythonReceipt $receipt;

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->prepareExecution($input, $output, new PythonReceipt($this->fs));

        $io = new SymfonyStyle($input, $output);

        foreach ($this->receipt->getPropertyQuestionsPairs() as $propertyQuestionPair) {
            $this->feedReceipt($propertyQuestionPair);    
        }

        $response = $this->askYesOrNo("Does you need to add a database?");
        if ($response) {
            print("The user wants a database.\n");
        } else {
            print("The user don't need a database.\n");
        }

        $dirPath = $this->askForReceiptFolderAndWriteFiles();

        $io->success(sprintf("Project created in %1\$s.", $dirPath));

        return Command::SUCCESS;
    }
}
