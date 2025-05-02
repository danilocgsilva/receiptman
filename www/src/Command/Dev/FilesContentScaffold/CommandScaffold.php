<?php

declare(strict_types=1);

namespace App\Command\Dev\FilesContentScaffold;

class CommandScaffold implements CommandScaffoldInterface
{
    public static function getContent(string $baseName): string
    {
        return <<<EOF
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
        class {$baseName}Command extends ReceiptmanCommand
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
    }
}
