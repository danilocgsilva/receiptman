<?php

declare(strict_types=1);

namespace App\Command\Dev\FilesContentScaffold;

interface CommandScaffoldInterface
{
    /**
     * Returns the content of the command scaffold.
     */ 
    public static function getContent(string $baseName): string;
}
