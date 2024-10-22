<?php

namespace App\Command;

use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use DateTime;
use Symfony\Component\Filesystem\Filesystem;
use App\ReceiptApp\PhpDevMysql;
use Symfony\Component\Console\Question\Question;

#[AsCommand(
    name: 'receipt:create',
    description: 'Add a short description for your command',
)]
class ReceiptCreateCommand extends Command
{
    private Filesystem $fs;

    private $input;

    private $output;

    private $questionHelper;
    
    public function __construct()
    {
        parent::__construct();
        $this->fs = new Filesystem();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $baseFolderName = $this->getBaseDateString();
        $dirPath = 'output' . DIRECTORY_SEPARATOR . $baseFolderName;
        
        $helper = $this->getHelper('question');

        $receipt = new PhpDevMysql();

        $questionContainerName = new Question("Write the container name\n");
        $containerName = $helper->ask($input, $output, $questionContainerName);
        $receipt->setName($containerName);

        $questionHttpRedirection = new Question("Write the port number redirection for http\n");
        $httpPortNumberRedirection = $helper->ask($input, $output, $questionHttpRedirection);
        $receipt->setHttpPortRedirection($httpPortNumberRedirection);
        
        $questionMysqlPortRedirection = new Question("Write the port number redirection for mysql\n");
        $mysqlPortNumberRedirection = $helper->ask($input, $output, $questionMysqlPortRedirection);
        $receipt->setMysqlPortRedirection($mysqlPortNumberRedirection);

        $questionMysqlRootPassword = new Question("Write the mysql root password\n");
        $mysqlRootPassword = $helper->ask($input, $output, $questionMysqlRootPassword);
        $receipt->setMysqlRootPassword($mysqlRootPassword);

        $this->makerFile($dirPath,$receipt);

        $io->success(sprintf("Project created in %1\$s.", $dirPath));

        return Command::SUCCESS;
    }

    private function getBaseDateString(): string
    {
        return (new DateTime())->format('Ymd-his');
    }

    private function makerFile(string $dirPath, PhpDevMysql $receipt)
    {
        if ($this->fs->exists($dirPath)) {
            throw new Exception(sprintf("The path %1\$s exists. Action aborted with exception.", $dirPath));
        }
        $this->fs->mkdir($dirPath);

        $files = $receipt->getFiles();
        foreach ($files as $file) {
            $file->write($dirPath, $this->fs);
        }
    }
}
