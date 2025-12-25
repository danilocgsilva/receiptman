<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Filesystem\Filesystem;

abstract class ReceiptmanCommand extends Command
{
    protected array $additionalReceipts = [];
    
    public function __construct(Filesystem $filesystem)
    {
        parent::__construct();
        $this->fs = $filesystem;
    }
}
