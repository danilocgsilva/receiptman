<?php

declare(strict_types=1);

namespace App\Command\Dev;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\ReceiptApp\File;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Filesystem\Filesystem;

#[AsCommand(
    name: 'admin:add',
    description: 'Add scaffold code to add a new receipt.',
)]
class AddNew extends Command
{
    private InputInterface $input;

    private const BASE_DIRECTORY = __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "..";

    protected Filesystem $fs;

    private OutputInterface $output;

    private SymfonyStyle $io;

    public function __construct()
    {
        parent::__construct();
        $this->fs = new Filesystem();
    }
    
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->injectInputOutput(input: $input, output: $output);
        
        $newReceiptName = $this->ask("What is the name of the new receipt? ");

        $this->createFileAndOutput("src/Command/{$newReceiptName}Command.php");
        $this->createFileAndOutput("src/ReceiptApp/Receipts/{$newReceiptName}Receipt.php");
        $this->io->writeln("Now it is required to custom the file content to match your needs. Find all occurrences of REPLACEME on each created file.");

        return Command::SUCCESS;
    }

    private function injectInputOutput(InputInterface $input, OutputInterface $output): void
    {
        $this->input = $input;
        $this->output = $output;

        $this->io = new SymfonyStyle($input, $output);
    }

    private function ask(string $question): string
    {
        $helper = $this->getHelper('question');
        $question = new Question('Writes the name of the new receipt: ');
        return $helper->ask(input: $this->input, output: $this->output, question: $question);
    }

    private function createFileAndOutput(string $fullPathName)
    {
        (new File(
            $fullPathName, 
            $this->getCommandContentScaffold())
        )->write(self::BASE_DIRECTORY, $this->fs);
        $this->io->writeln("* The file {$fullPathName} has just beign created.");
    }

    private function getCommandContentScaffold(): string
    {
        $commandScaffoldContent = <<<EOF
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

        #[AsCommand(
            name: 'REPLACEME1',
            description: 'REPPLACEME2',
        )]
        class REPLACEME3 extends ReceiptmanCommand
        {
            use PrepareExecution;

            protected function execute(InputInterface \$input, OutputInterface \$output): int
            {
                \$this->prepareExecution(\$input, \$output, new PhpReceipt());

                \$io = new SymfonyStyle(\$input, \$output);

                return Command::SUCCESS;
            }
        }
        EOF;

        return $commandScaffoldContent;
    }

    private function getReceiptContentScaffold(): string
    {
        $receiptContentScaffold = <<<EOF
        <?php

        declare(strict_types=1);

        namespace App\ReceiptApp\Receipts;

        use App\ReceiptApp\Receipts\Interfaces\ReceiptInterface;
        use App\ReceiptApp\File;
        use Symfony\Component\Yaml\Yaml;

        class REPLACEME1 extends ReceiptCommons implements ReceiptInterface
        {
            /**
             * @inheritDoc
             */
            public function getFiles(): array
            {
                return \$files;
            }

            private function buildYamlStructure(): void
            {
                \$this->yamlStructure = [
                    'services' => [
                        \$this->name => [
                            'image' => 'REPLACEME2',
                            'container_name' => \$this->name
                        ]
                    ]
                ];
            }
        }
        EOF;

        return $receiptContentScaffold;
    }
}
