<?php

namespace App\Command\Traits;

use Symfony\Component\Console\Question\Question;

trait ReceiptFolder
{
    private function askForReceiptFolderAndWriteFiles(): string
    {
        $questionFolderName = new Question("Would you like to set a name for directory receipt? If so, just type the directory name or keep it blank to set the default directory name. \n");
        $responseDirName = $this->questionHelper->ask($this->input, $this->output, $questionFolderName);
        $dirPath = $this->getDirPath($responseDirName);
        $allReceipts = array_merge([$this->receipt], $this->additionalReceipts);
        $this->makerFile($dirPath, ...$allReceipts);
        return $dirPath;
    }
}
