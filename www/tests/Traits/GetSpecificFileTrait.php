<?php

namespace App\Tests\Traits;

use App\ReceiptApp\File;

trait GetSpecificFileTrait
{
    private function getSpecificFile(array $files, $fileName): File
    {
        $fileFound = null;
        foreach ($files as $file) {
            if ($file->path === $fileName) {
                $fileFound = $file;
                break;
            }
        }
        return $fileFound;
    }
}
