<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Console\Application;
use App\Tests\Traits\MockFileSystemTrait;
use App\Command\DebianCommand;
use Symfony\Component\Console\Tester\CommandTester;

class DebianCommandTest extends TestCase
{
    use MockFileSystemTrait;

    #[Test]
    public function amountQuestionsDone()
    {
        $application = new Application();
        $fileSystemMocked = $this->getFileSystemMocked("output/my_debian_container", 3);
        $application->add(new DebianCommand($fileSystemMocked));
        $command = $application->find("receipt:debian");
        $commandTester = new CommandTester($command);
        $commandTester->setInputs([
            "my_debian_container", // -> container name
            "no", // -> use host network
            "no", // -> have a .ssh mounted to access through local machine
            "my_debian_container" // -> directory name
        ]);

        $commandTester->execute([]);
        $this->assertStringContainsString(
            sprintf("Project created in %1\$s.", 'output/my_debian_container'),
            $commandTester->getDisplay()
        );
    }
}
