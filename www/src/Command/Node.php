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
use App\ReceiptApp\Receipts\NodeReceipt;
use Symfony\Component\Console\Question\ConfirmationQuestion;

#[AsCommand(
    name: 'receipt:node',
    description: 'Node Receipt',
)]
class Node extends Command
{
    use PrepareExecution;
    use ReceiptFolder;

    private Filesystem $fs;

    private $input;

    private $output;

    private $questionHelper;

    private NodeReceipt $receipt;
    
    public function __construct()
    {
        parent::__construct();
        $this->fs = new Filesystem();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->prepareExecution($input, $output, new NodeReceipt());

        $io = new SymfonyStyle($input, $output);

        foreach ($this->receipt->getPropertyQuestionsPairs() as $propertyQuestionPair) {
            $this->feedReceipt($propertyQuestionPair);    
        }

        $questionInfinitLoop = new ConfirmationQuestion("Should an infinit loop should be applied, so the container does not halts in initialization?\n", true);
        if ($this->questionHelper->ask($this->input, $this->output, $questionInfinitLoop)) {
            $this->receipt->setInfinitLoop();
        }

        $dirPath = $this->askForReceiptFolder();

        $io->success(sprintf("Project created in %1\$s.", $dirPath));

        return Command::SUCCESS;
    }
}
