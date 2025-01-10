<?php

declare(strict_types=1);

namespace App\Tests\Command;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use App\Command\PhpFullDev;
use Symfony\Component\Console\Tester\CommandTester;

class PhpFullDevTest extends TestCase
{
    public function testPhpFullDev()
    {
        // $application = new Application();
        // $application->add(new PhpFullDev());

        // $command = $application->find("receipt:php-full-dev");
        // $commandTester = new CommandTester($command);
        // $commandTester->execute([]);
    }
}
