<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use App\ReceiptApp\Traits\PrepareExecution;
use App\ReceiptApp\Receipts\NodeReceipt;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

#[AsCommand(
    name: 'receipt:node',
    description: 'Node Receipt',
)]
class Node extends Command
{
    use PrepareExecution;

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
            $this->feedReceipt($propertyQuestionPair[0], $propertyQuestionPair[1]);    
        }
        $questionInfinitLoop = new ConfirmationQuestion("Should an infinit loop should be applied, so the container does not halts in initialization?\n", true);
        if ($this->questionHelper->ask($this->input, $this->output, $questionInfinitLoop)) {
            $this->receipt->setInfinitLoop();
        }
        $questionFolderName = new Question("Would you like to set a name for directory receipt? If so, just type the directory name or keep it blank to set the default directory name. \n");
        
        $responseDirName = $this->questionHelper->ask($this->input, $this->output, $questionFolderName);
        $dirPath = $this->getDirPath($responseDirName);

        $this->makerFile($dirPath,$this->receipt);

        $io->success(sprintf("Project created in %1\$s.", $dirPath));

        return Command::SUCCESS;
    }
}
