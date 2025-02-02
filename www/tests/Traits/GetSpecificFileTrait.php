<?php

declare(strict_types=1);

namespace App\Tests\Traits;

use App\ReceiptApp\File;
use Exception;

trait GetSpecificFileTrait
{
    private function getSpecificFile(array $files, $fileName): File
    {
        $fileFound = null;
        foreach ($files as $file) {
            if ($file->path === $fileName) {
                return $file;
            }
        }
        throw new Exception("File not beign conteined in the array: {$fileName}.");
    }
}
