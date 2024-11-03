<?php

namespace App\ReceiptApp;

use Symfony\Component\Filesystem\Filesystem;

class File
{
    public function __construct(public readonly string $path, public readonly string $content) {}

    public function write(string $baseDirectory, Filesystem $fs): void
    {
        $fullPath = $baseDirectory . DIRECTORY_SEPARATOR . $this->path;
        $this->makeDirectoriesIfRequired($fullPath);
        $fs->touch($fullPath);
        $fs->appendToFile($fullPath, $this->content);
    }

    private function makeDirectoriesIfRequired(string $fullPathDirectory): void
    {
        $dirParts = explode(DIRECTORY_SEPARATOR, $fullPathDirectory);
        array_pop($dirParts);
        $checkDir = array_shift($dirParts);
        foreach ($dirParts as $dirPart) {
            $currentCheck = $checkDir . DIRECTORY_SEPARATOR . $dirPart;
            if (!is_dir($currentCheck)) {
                mkdir($currentCheck);
            }
            $checkDir = $currentCheck;
        }
    }
}
