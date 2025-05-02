<?php

declare(strict_types=1);

namespace App\Command\Dev\FilesContentScaffold;

class FilesContent
{
    public function __construct(private string $baseName)
    {
    }

    public function getCommandContent(): string
    {
        return CommandScaffold::getContent($this->baseName);
    }

    public function getReceiptContent(): string
    {
        return ReceiptScaffold::getContent($this->baseName);
    }
    public function getQuestionsContent(): string
    {
        return QuestionsScaffold::getContent($this->baseName);
    }
}
