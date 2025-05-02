<?php

declare(strict_types=1);

namespace App\Tests\Command;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use App\Command\PhpFullDevCommand;
use App\Tests\Traits\MockFileSystemTrait;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;

class PhpFullDevCommandTest extends TestCase
{
    use MockFileSystemTrait;

    public function testPhpFullDev()
    {
        $fileSystemMocked = $this->getFileSystemMocked();
        
        $application = new Application();
        $application->add(new PhpFullDevCommand($fileSystemMocked));
        $command = $application->find("receipt:php-full-dev");
        $commandTester = new CommandTester($command);

        $commandTester->setInputs([
            "no", "the_container_test2", 
            "no", "no", 
            "3306", "no", 
            "no", "no",
            "the_container_test2"
        ]);

        $commandTester->execute([]);
    }

    private function getFileSystemMocked(): Filesystem
    {
        $fileSystemMocked = $this->getMockBuilder(Filesystem::class)->getMock();
        $matcher = $this->exactly(6);
        $fileSystemMocked
            ->expects($matcher)
            ->method('touch')
            ->willReturnCallback(function (string $providedArgumentValueForNThTime) use ($matcher) {
                match ($matcher->numberOfInvocations()) {
                    1 =>  $this->assertEquals("output/the_container_test2/docker-compose.yml", $providedArgumentValueForNThTime),
                    2 =>  $this->assertEquals("output/the_container_test2/Dockerfile", $providedArgumentValueForNThTime),
                    3 =>  $this->assertEquals("output/the_container_test2/config/xdebug.ini", $providedArgumentValueForNThTime),
                    4 =>  $this->assertEquals("output/the_container_test2/config/startup.sh", $providedArgumentValueForNThTime),
                    5 =>  $this->assertEquals("output/the_container_test2/config/apache2.conf", $providedArgumentValueForNThTime),
                    6 =>  $this->assertEquals("output/the_container_test2/www/html/index.php", $providedArgumentValueForNThTime),
                };
            });

        return $fileSystemMocked;
    }
}
