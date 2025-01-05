<?php

declare(strict_types=1);

namespace App\Dev\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\ReceiptApp\File;

#[AsCommand(
    name: 'admin:add',
    description: 'Add scaffold code to add a new receipt.',
)]
class AddNew
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $files = [
            new File('Command', $this->getCommandContentScaffold()) ,
            'Receipt'
        ];

        return Command::SUCCESS;
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
        class REPLACEME3 extends Command
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
