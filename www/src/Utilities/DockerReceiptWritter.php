<?php

namespace App\Utilities;

use App\Utilities\DockerLineCommands;

class DockerReceiptWritter implements DockerReceiptWritterInterface
{
    private array $commands = [];
    
    public function dump(): string
    {
        if (count($this->commands) === 0) {
            return '';
        }

        return implode("\n", array_map(function ($command) {
            return $command->dump();
        }, $this->commands));
    }

    public function addBlankLine(): self
    {
        $this->commands[] = new DockerLineCommands\BlankLine();
        return $this;
    }

    public function addRawContent(string $content): self
    {
        $this->commands[] = new DockerLineCommands\RawContent($content);
        return $this;
    }

    public function addAptGetUpdate()
    {
        $this->commands[] = new DockerLineCommands\AptGetUpdate();
        return $this;
    }

    public function addAptGetUpgrade()
    {
        $this->commands[] = new DockerLineCommands\AptGetUpgrade();
        return $this;
    }

    public function addInstallPackages(array $packages): self
    {
        $this->commands[] = new DockerLineCommands\InstallPackages($packages);
        return $this;
    }
}
