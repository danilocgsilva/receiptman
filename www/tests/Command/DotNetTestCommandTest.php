<?php

declare(strict_types=1);

use App\Command\DotNetCommand;
use App\Tests\Traits\MockFileSystemTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Console\Tester\CommandTester;

class DotNetTestCommandTest extends TestCase
{
    use MockFileSystemTrait;
    
    #[Test]
    public function amountQuestionsDone()
    {
        $application = new Application();
        $fileSystemMocked = $this->getFileSystemMocked("output/my_dotnet_first_container", 3);
        $application->add(new DotNetCommand($fileSystemMocked));
        $command = $application->find("receipt:dotnet");
        $commandTester = new CommandTester($command);
        $commandTester->setInputs([
            "my_dotnet_first_container", "no",
            "no", "no", 
            "no", "my_dotnet_first_container"
        ]);

        $commandTester->execute([]);
    }

    #[Test]
    public function amountQuestionsDoneWithDatabase()
    {
        $application = new Application();
        $fileSystemMocked = $this->getFileSystemMocked("output/my_dotnet_first_container", 3);
        $application->add(new DotNetCommand($fileSystemMocked));
        $command = $application->find("receipt:dotnet");
        $commandTester = new CommandTester($command);
        $commandTester->setInputs([
            "my_dotnet_first_container", "no",
            "no", "no", 
            "yes", "3306",
            "somestrongpassword", "my_dotnet_first_container"
        ]);

        $commandTester->execute([]);
    }
}
