<?php

declare(strict_types=1);

namespace App\ReceiptApp\Receipts;

use App\ReceiptApp\File;
use App\ReceiptApp\Receipts\Interfaces\PhpInterface;
use App\ReceiptApp\Receipts\Interfaces\ReceiptInterface;
use App\ReceiptApp\Receipts\Questions\BaseQuestion;
use Symfony\Component\Filesystem\Filesystem;
use App\ReceiptApp\Receipts\Questions\Types\QuestionEntry;
USE App\ReceiptApp\Receipts\Questions\Types\InputType;

class PhpReceipt extends ReceiptCommons implements ReceiptInterface, PhpInterface
{
    private string $phpVersion = "latest";
    private bool $infinityLoop = false;

    private bool $composer = false;

    private bool $xdebug = false;
    
    private array $versionMapping = [
        '8.5' => '8.5.1',
    ];
    
    public function __construct(Filesystem $fs)
    {
        parent::__construct($fs);

        $this->questionsPairs = array_merge(
            (new BaseQuestion())->getPropertyQuestionPair(),
            [
                new QuestionEntry(
                    methodName: "setPhpVersion",
                    textQuestion: "Write the PHP version to use \n",
                )
            ],
            [
                new QuestionEntry(
                    methodName: "setComposer",
                    textQuestion: "Should I install Composer? \n",
                    inputType: InputType::yesorno
                )
            ]
        );
    }

    public function setComposer(): self
    {
        $this->composer = true;
        return $this;
    }

    public function setXdebug(): self
    {
        $this->xdebug = true;
        return $this;
    }

    public function setPhpVersion(string $phpVersion): static
    {
        $this->phpVersion = $phpVersion;
        return $this;
    }

    public function setInfinitLoop(): void
    {
        $this->infinityLoop = true;
    }

    public function getFiles(): array
    {
        $files = [];
        if ($this->infinityLoop || $this->xdebug) {
            $files[] = new File('Dockerfile', $this->getDockerfileContent(), $this->fs);
        }
        if ($this->xdebug) {
            $files[] = new File('config/xdebug.ini', $this->getXdebugIniContent(), $this->fs);
        }
        return $files;
    }

    private function getDockerfileContent(): string
    {
        $imageTag = $this->versionMapping[$this->phpVersion] ?? $this->phpVersion;

        $recipeLines = [
            "FROM php:{$imageTag}",
            "",
        ];

        if ($this->composer) {
            $recipeLines[] = "RUN php -r \"copy('https://getcomposer.org/installer', 'composer-setup.php');\"";
            $recipeLines[] = "RUN php composer-setup.php --install-dir=/usr/local/bin --filename=composer";
            $recipeLines[] = "";
        }

        if ($this->xdebug) {
            $recipeLines[] = "RUN pecl install xdebug && docker-php-ext-enable xdebug";
            $recipeLines[] = "COPY /config/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini";
            $recipeLines[] = "";
        }

        if ($this->infinityLoop) {
            $recipeLines[] = "CMD while : ; do sleep 1000; done";
        }

        return implode("\n", $recipeLines);
    }

    private function getXdebugIniContent(): string
    {
        return <<<INI
        zend_extension=xdebug.so

        xdebug.mode=debug
        xdebug.start_with_request=yes
        xdebug.client_host=host.docker.internal
        xdebug.client_port=9003
        INI;
    }

    protected function buildYamlStructure(): void
    {
        $imageTag = $this->versionMapping[$this->phpVersion] ?? $this->phpVersion;
        
        $this->yamlStructure = [
            $this->name => []
        ];

        if ($this->infinityLoop || $this->xdebug) {
            $this->yamlStructure[$this->name]['build'] = [
                'context' => '.'
            ];
        } else {
            $this->yamlStructure[$this->name]['image'] = 'php:' . $imageTag;
        }

        $this->yamlStructure[$this->name]['container_name'] = $this->name;

        if ($this->networkModeHost) {
            $this->yamlStructure[$this->name]['network_mode'] = 'host';
        }
    }
}
