<?php

declare(strict_types=1);

namespace App\ReceiptApp\Traits;

use App\ReceiptApp\Receipts\Questions\Types\QuestionEntry;
use App\ReceiptApp\Receipts\Interfaces\ReceiptInterface;
use App\Utilities\WrapServicesYamlStructure;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use DateTime;
use Symfony\Component\Console\Question\{Question, ConfirmationQuestion};
use Exception;
use InvalidArgumentException;
use App\ReceiptApp\Receipts\Questions\Types\InputType;
use App\ReceiptApp\File;
use Symfony\Component\Yaml\Yaml;

trait PrepareExecution
{
    private function prepareExecution(
        InputInterface $input,
        OutputInterface $output,
        ReceiptInterface $receipt
    ): void {
        $this->input = $input;
        $this->output = $output;
        $this->questionHelper = $this->getHelper('question');
        $this->receipt = $receipt;
    }

    private function getBaseDateString(): string
    {
        return (new DateTime())->format('Ymd-his');
    }

    private function getDirPath(string|null $baseFolderName = null): string
    {
        if (!$baseFolderName) {
            $baseFolderName = $this->getBaseDateString();
        }
        return 'output' . DIRECTORY_SEPARATOR . $baseFolderName;
    }

    /**
     * @param \App\ReceiptApp\Receipts\Questions\Types\QuestionEntry $questionEntry
     * @throws \InvalidArgumentException
     * @return void
     */
    private function feedReceipt(QuestionEntry $questionEntry, ReceiptInterface|null $currentReceipt)
    {
        $receiptInSetup = $this->receipt;
        if ($questionEntry->inputType === null) {
            if (!$questionEntry->textQuestion || !$questionEntry->methodName) {
                throw new InvalidArgumentException("Question text and method name are required for non-yes/no questions.");
            }
            $answer = $this->makeQuestionAndGetAnswer($questionEntry->textQuestion);
            $receiptInSetup->{$questionEntry->methodName}($answer);
            return;
        }

        $answer = $this->askYesOrNo($questionEntry->textQuestion);
        switch ($questionEntry->inputType) {
            case InputType::yesorno:
                if ($answer) {
                    $receiptInSetup->{$questionEntry->methodName}();
                }
                break;

            case InputType::yesornoinverse:
                if (!$answer) {
                    $receiptInSetup->{$questionEntry->methodName}();
                }
                break;

            default:
                throw new InvalidArgumentException(
                    "Unsupported input type: " . (string) $questionEntry->inputType
                );
        }
    }

    private function askYesOrNo(string $questionTitle): bool
    {
        $yesOrNoQuestion = new ConfirmationQuestion($questionTitle);
        $response = $this->questionHelper->ask($this->input, $this->output, $yesOrNoQuestion);
        return $response;
    }

    private function makeQuestionAndGetAnswer(string $questionTitle): string
    {
        $question = new Question($questionTitle);
        return (string) $this->questionHelper->ask($this->input, $this->output, $question);
    }

    private function makerFile(string $dirPath, ReceiptInterface ...$allReceipts)
    {
        if ($this->fs->exists($dirPath)) {
            throw new Exception(sprintf("The path %1\$s exists. Action aborted with exception.", $dirPath));
        }
        $this->fs->mkdir($dirPath);

        foreach ($allReceipts as $receipt) {
            // $receipt->write($dirPath);
            /** @var \App\ReceiptApp\File $file */
            foreach ($receipt->getFiles() as $file) {
                $file->write($dirPath);
            }
        }


        // if ($additionalReceipts) {
        //     $allReceipts = [$receipt, ...$additionalReceipts];
        //     foreach ($additionalReceipts as $additionalReceipt) {
        //         foreach ($additionalReceipt->getFiles() as $file) {
        //             $file->write($dirPath);
        //         }
        //     }
        // } else {
        //     $allReceipts = [$receipt];
        // }

        $serviceYamlWrapper = new WrapServicesYamlStructure(...$allReceipts);

        (new File(
            'docker-compose.yml',
            Yaml::dump($serviceYamlWrapper->getFullDockerComposeYamlStructure(), 4, 2),
            $this->fs
        ))->write($dirPath);
    }
}
