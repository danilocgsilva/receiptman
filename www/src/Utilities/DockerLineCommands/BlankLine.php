<?php

namespace App\Utilities\DockerLineCommands;

use App\Utilities\DockerReceiptWritterInterface;

class BlankLine implements DockerReceiptWritterInterface
{
    public function dump(): string
    {
        return "";
    }
}
