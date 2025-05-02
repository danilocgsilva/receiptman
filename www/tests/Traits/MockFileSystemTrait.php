<?php

declare(strict_types=1);

namespace App\Tests\Traits;

use Symfony\Component\Filesystem\Filesystem;

trait MockFileSystemTrait
{
    public function getFileSystemMocked(string $path, int $expectedMkdirInvokations): mixed
    {
        $fileSystemMocked = $this->getMockBuilder(Filesystem::class)->getMock();
        $fileSystemMocked
            ->expects($this->exactly($expectedMkdirInvokations))
            ->method('mkdir')
            ->with($path);
        return $fileSystemMocked;
    }
}
