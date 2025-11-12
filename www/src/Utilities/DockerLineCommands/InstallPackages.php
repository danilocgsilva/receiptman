<?php

namespace App\Utilities\DockerLineCommands;

use App\Utilities\DockerReceiptWritterInterface;

class InstallPackages implements DockerReceiptWritterInterface
{
    private array $packages;

    public function __construct(array $packages)
    {
        $this->packages = $packages;
    }

    public function dump(): string
    {
        return "RUN apt-get install -y " . implode(" ", $this->packages);
    }
}
