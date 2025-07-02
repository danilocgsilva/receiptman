<?php

namespace App\Utilities\DockerLineCommands;

use App\Utilities\DockerReceiptWritterInterface;

class AptGetUpgrade implements DockerReceiptWritterInterface
{
    public function dump(): string
    {
        return "RUN apt-get upgrade -y";
    }
}
