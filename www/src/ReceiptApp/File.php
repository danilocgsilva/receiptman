<?php

declare(strict_types=1);

namespace App\ReceiptApp;

use Symfony\Component\Filesystem\Filesystem;

class File
{
    public function __construct(
        public readonly string $path, 
        public readonly string $content,
        private Filesystem $fs
    ) {}

    public function write(string $baseDirectory): void
    {
        $fullPath = $baseDirectory . DIRECTORY_SEPARATOR . $this->path;
        $this->makeDirectoriesIfRequired($fullPath);
        $this->fs->touch($fullPath);
        $this->fs->appendToFile($fullPath, $this->content);
    }

    private function makeDirectoriesIfRequired(string $fullPathDirectory): void
    {
        $dirParts = explode(DIRECTORY_SEPARATOR, $fullPathDirectory);
        array_pop($dirParts);
        $checkDir = array_shift($dirParts);
        foreach ($dirParts as $dirPart) {
            $currentCheck = $checkDir . DIRECTORY_SEPARATOR . $dirPart;
            if (!is_dir($currentCheck)) {
                $this->fs->mkdir($currentCheck);
            }
            $checkDir = $currentCheck;
        }
    }
}
