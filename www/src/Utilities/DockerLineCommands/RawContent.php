<?php

namespace App\Utilities\DockerLineCommands;

use App\Utilities\DockerReceiptWritterInterface;

class RawContent implements DockerReceiptWritterInterface
{
    private string $content;

    public function __construct(string $content)
    {
        $this->content = $content;
    }

    public function dump(): string
    {
        return $this->content;
    }
}
