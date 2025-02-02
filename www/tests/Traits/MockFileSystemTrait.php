<?php

declare(strict_types=1);

namespace App\Tests\Traits;

use Symfony\Component\Filesystem\Filesystem;

trait MockFileSystemTrait
{
    public function getFileSystemMocked(string $path): mixed
    {
        $fileSystemMocked = $this->getMockBuilder(Filesystem::class)->getMock();
        $fileSystemMocked
            ->expects($this->once())
            ->method('mkdir')
            ->with($path);
        return $fileSystemMocked;
    }
}
