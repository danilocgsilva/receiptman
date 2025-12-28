<?php

declare(strict_types=1);

namespace App\Command;

use App\Command\Traits\ReceiptFolder;
use App\ReceiptApp\Traits\PrepareExecution;
use App\ReceiptApp\Receipts\PhpReceipt;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Question\ConfirmationQuestion;

#[AsCommand(
    name: 'receipt:php',
    description: 'Php receipt',
)]
class PhpCommand extends ReceiptmanCommand
{
    use PrepareExecution;
    use ReceiptFolder;

    private PhpReceipt $receipt;

    protected Filesystem $fs;

    private $input;

    private $output;

    private QuestionHelper $questionHelper;

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->prepareExecution($input, $output, new PhpReceipt($this->fs));

        $io = new SymfonyStyle($input, $output);

        while ($propertyQuestionPair = $this->receipt->getNextQuestionPair()) {
            $this->feedReceipt($propertyQuestionPair, $this->receipt);
        }

        $questionInfinitLoop = new ConfirmationQuestion("Should an infinit loop should be applied, so the container does not halts in initialization?\n", true);
        if ($this->questionHelper->ask($this->input, $this->output, $questionInfinitLoop)) {
            $this->receipt->setInfinitLoop();
        }

        $dirPath = $this->askForReceiptFolderAndWriteFiles();

        $io->success(sprintf("Project created in %1\$s.", $dirPath));

        return Command::SUCCESS;
    }
}
