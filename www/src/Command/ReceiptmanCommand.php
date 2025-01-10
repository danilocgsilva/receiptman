<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Filesystem\Filesystem;

abstract class ReceiptmanCommand extends Command
{
    public function __construct()
    {
        parent::__construct();
        $this->fs = new Filesystem();
    }
}
