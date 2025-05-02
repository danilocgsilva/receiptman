<?php

declare(strict_types=1);

namespace App\Command;

use App\Command\Traits\ReceiptFolder;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\ReceiptApp\Traits\PrepareExecution;
use App\ReceiptApp\Receipts\NodeReceipt;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Filesystem\Filesystem;

#[AsCommand(
    name: 'receipt:node',
    description: 'Node Receipt',
)]
class NodeCommand extends ReceiptmanCommand
{
    use PrepareExecution;
    use ReceiptFolder;

    protected Filesystem $fs;

    private $input;

    private $output;

    private $questionHelper;

    private NodeReceipt $receipt;

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->prepareExecution($input, $output, new NodeReceipt($this->fs));

        $io = new SymfonyStyle($input, $output);

        foreach ($this->receipt->getPropertyQuestionsPairs() as $propertyQuestionPair) {
            $this->feedReceipt($propertyQuestionPair);    
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
