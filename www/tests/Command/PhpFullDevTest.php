<?php

declare(strict_types=1);

namespace App\Tests\Command;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use App\Command\PhpFullDev;
use App\Tests\Traits\MockFileSystemTrait;
use Symfony\Component\Console\Tester\CommandTester;


class PhpFullDevTest extends TestCase
{
    use MockFileSystemTrait;

    public function testPhpFullDev()
    {
        $application = new Application();

        $fileSystemMocked = $this->getFileSystemMocked("output/the_container_test2");

        $application->add(new PhpFullDev($fileSystemMocked));

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
}
