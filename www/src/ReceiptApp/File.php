<?php

namespace App\ReceiptApp;

use Symfony\Component\Filesystem\Filesystem;

class File
{
    public function __construct(private string $path, private string $content) {}

    public function write(string $baseDirectory, Filesystem $fs): void
    {
        $fullPath = $baseDirectory . DIRECTORY_SEPARATOR . $this->path;
        $fs->touch($fullPath);
        $fs->appendToFile($fullPath, $this->content);
    }
}
