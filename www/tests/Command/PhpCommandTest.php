<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Tests\Traits\MockFileSystemTrait;
use Symfony\Component\Console\Application;
use Symfony\Component\Filesystem\Filesystem;
use App\Command\PhpCommand;
use Symfony\Component\Console\Tester\CommandTester;

class PhpCommandTest extends TestCase
{
    use MockFileSystemTrait;

    public function testCommand()
    {
        $fileSystemMocked = $this->getFileSystemMocked();

        $application = new Application();
        $application->add(new PhpCommand($fileSystemMocked));
        $command = $application->find("receipt:php");
        $commandTester = new CommandTester($command);

        $commandTester->setInputs([
            "my_test_php_container", "no", "no",
            "8.4", "yes", "yes", "my_test_php_container"
        ]);

        $commandTester->execute([]);
    }

    private function getFileSystemMocked(): Filesystem
    {
        $fileSystemMocked = $this->getMockBuilder(Filesystem::class)->getMock();
        $matcher = $this->exactly(2);
        $fileSystemMocked
            ->expects($matcher)
            ->method('touch')
            ->willReturnCallback(function (string $providedArgumentValueForNThTime) use ($matcher) {
                match ($matcher->numberOfInvocations()) {
                    1 =>  $this->assertEquals("output/my_test_php_container/Dockerfile", $providedArgumentValueForNThTime),
                    2 =>  $this->assertEquals("output/my_test_php_container/docker-compose.yml", $providedArgumentValueForNThTime),
                };
            });

        return $fileSystemMocked;
    }
}
