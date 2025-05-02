<?php

declare(strict_types=1);

use App\Command\MySQLCommand;
use Symfony\Component\Console\Tester\CommandTester;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Filesystem\Filesystem;
use PHPUnit\Framework\MockObject\Rule\InvokedCount;

class MySQLCommandTest extends TestCase
{
    public function testCommand()
    {
        $fileSystemMocked = $this->getMockedFileSystem();

        $application = new Application();
        $application->add(new MySQLCommand($fileSystemMocked));
        $command = $application->find("receipt:mysql");
        $commandTester = new CommandTester($command);

        $commandTester->setInputs([
            "my_test_mysql_container", "no",
            "no", "3306",
            "verystrongpassword", "my_receipt_directory"
        ]);

        $commandTester->execute([]);
    }

    private function getMockedFileSystem()
    {
        $fileSystemMocked = $this->getMockBuilder(Filesystem::class)->getMock();

        /**
         * @var InvokedCount $matcher
         */
        $matcher = $this->exactly(1);

        $fileSystemMocked
            ->expects($matcher)
            ->method('touch')
            ->willReturnCallback(function (string $providedArgumentValueForNThTime) use ($matcher) {
                if ($matcher->numberOfInvocations() === 1) {
                    $this->assertEquals("output/my_receipt_directory/docker-compose.yml", $providedArgumentValueForNThTime);
                }
            });

        return $fileSystemMocked;
    }
}
