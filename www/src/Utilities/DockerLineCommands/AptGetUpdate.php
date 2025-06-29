<?php

namespace App\Utilities\DockerLineCommands;

use App\Utilities\DockerReceiptWritterInterface;

class AptGetUpdate implements DockerReceiptWritterInterface
{
    public function dump(): string
    {
        return "RUN apt-get update";
    }
}
