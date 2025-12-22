<?php

declare(strict_types=1);

namespace App\Tests\Command;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use App\Command\PythonCommand;
use Symfony\Component\Filesystem\Filesystem;

class PythonCommandTest extends TestCase
{
    public function testAskingQuestions(): void
    {
        $application = new Application();
        $application->add(new PythonCommand($this->getFileSystemMocked()));
        $command = $application->find("receipt:python");
        $commandTester = new CommandTester($command);

        $commandTester->setInputs([
            "my_testing_container", "no",
            "no",
            "yes", "yes", 
            "my_testing_container"
        ]);

        $commandTester->execute([]);
        $this->assertStringContainsString(
            sprintf("Project created in %1\$s.", 'output/my_testing_container'),
            $commandTester->getDisplay()
        );
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
                    1 =>  $this->assertEquals("output/my_testing_container/Dockerfile", $providedArgumentValueForNThTime),
                    2 =>  $this->assertEquals("output/my_testing_container/docker-compose.yml", $providedArgumentValueForNThTime),
                };
            });

        return $fileSystemMocked;
    }
}
