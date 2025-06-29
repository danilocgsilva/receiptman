<?php

namespace App\Utilities;

interface DockerReceiptWritterInterface
{
    public function dump(): string;
}
