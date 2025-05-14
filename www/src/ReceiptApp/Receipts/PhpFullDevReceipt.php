<?php

declare(strict_types=1);

namespace App\ReceiptApp\Receipts;

use App\ReceiptApp\Receipts\Interfaces\ReceiptInterface;
use Symfony\Component\Yaml\Yaml;
use App\ReceiptApp\File;
use App\ReceiptApp\Receipts\Questions\PhpDevMysqlQuestions;
use App\ReceiptApp\Traits\{
    HttpPortRedirection,
    RemoveQuestionByMethod
};
use Symfony\Component\Filesystem\Filesystem;
use App\ReceiptApp\Receipts\NotReadyException;
use App\ReceiptApp\Receipts\ConfigurationsDataTraits\ApacheConfigurationContentGeneratorTrait;
use Exception;

class PhpFullDevReceipt extends ReceiptCommons implements ReceiptInterface
{
    use HttpPortRedirection;
    use RemoveQuestionByMethod;
    use ApacheConfigurationContentGeneratorTrait;

    private string $mysqlPortRedirection;

    private string $mysqlRootPassword;

    private bool $appDir = false;

    private bool $rootNameAsPublic = false;

    private bool $node = false;

    private bool $onDatabase = true;

    private string $phpVersion = "8.2";

    public function __construct(Filesystem $fs)
    {
        parent::__construct($fs);
        $this->questionsPairs = (new PhpDevMysqlQuestions())->getPropertyQuestionPair();
    }

    public function setMysqlPortRedirection(string $mysqlPortRedirection): static
    {
        $this->mysqlPortRedirection = $mysqlPortRedirection;
        return $this;
    }

    public function setMysqlRootPassword(string $mysqRootPassword): static
    {
        $this->mysqlRootPassword = $mysqRootPassword;
        return $this;
    }

    public function setAppFolder(): self
    {
        $this->appDir = true;
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function getFiles(): array
    {
        if (
            !isset($this->httpPortRedirection) ||
            !isset($this->name)
        ) {
            throw new NotReadyException($this);
        }

        $this->buildYamlStructure();
        
        $files = [
            new File("docker-compose.yml", Yaml::dump($this->yamlStructure, 4, 2), $this->fs),
            new File("Dockerfile", $this->getDockerfile(), $this->fs),
            new File("config/xdebug.ini", $this->getXDebugContent(), $this->fs),
            new File("config/startup.sh", $this->getStartupContent(), $this->fs),
            new File("config/apache2.conf", $this->getApacheConfigs(), $this->fs)
        ];

        if ($this->appDir) {
            $files[] = new File("app/.gitkeep", "", $this->fs);
        } else {
            if ($this->rootNameAsPublic) {
                $files[] = new File("config/000-default.conf", $this->get000defaultFileContent(), $this->fs);
                $files[] = new File("www/public/index.php", "<?php\necho \"Be happy!\";", $this->fs);
            } else {
                $files[] = new File("www/html/index.php", "<?php\necho \"Be happy!\";", $this->fs);
            }
        }

        return $files;
    }

    /**
     * @return \App\ReceiptApp\Receipts\Questions\Types\QuestionEntry[]
     */
    public function getPropertyQuestionsPairs(): array
    {
        if (!$this->onDatabase) {
            $this->removeQuestionByMethod("setMysqlPortRedirection", $this->questionsPairs);
            $this->removeQuestionByMethod("setMysqlRootPassword", $this->questionsPairs);
        }

        return $this->questionsPairs;
    }

    public function setPublicFolderAsHost(): self
    {
        $this->rootNameAsPublic = true;
        return $this;
    }

    public function setNoDatabase(): static
    {
        $this->onDatabase = false;

        $this->removeQuestionByMethod("setMysqlPortRedirection", $this->questionsPairs);
        $this->removeQuestionByMethod("setMysqlRootPassword", $this->questionsPairs);
        
        return $this;
    }

    public function addNode(): static
    {
        $this->node = true;
        
        return $this;
    }

    public function setPhpVersion(string $phpVersion): static
    {
        $this->phpVersion = $phpVersion;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getHttpPortRedirection(): string
    {
        return $this->httpPortRedirection;
    }

    private function buildYamlStructure(): void
    {
        $this->yamlStructure = [
            'services' => [
                $this->name => [
                    'build' => [
                        'context' => '.'
                    ],
                    'container_name' => $this->name,
                    'volumes' => [],
                    'ports' => [
                        sprintf('%s:80', $this->httpPortRedirection)
                    ],
                    'working_dir' => ''
                ]
            ]
        ];

        if ($this->onDatabase) {
            $databaseReceipt = [
                'image' => 'mysql:latest',
                'container_name' => $this->name . '_db',
                'environment' => [
                    sprintf('MYSQL_ROOT_PASSWORD=%s', $this->mysqlRootPassword)
                ],
                'ports' => [
                    sprintf('%s:3306', $this->mysqlPortRedirection)
                ]
            ];

            $this->yamlStructure['services'][$this->name . '_db'] = $databaseReceipt;
        }

        if ($this->appDir) {
            $this->yamlStructure['services'][$this->name]['volumes'] = [
                './app:/app'
            ];
            $this->yamlStructure['services'][$this->name]['working_dir'] = '/app';
        } else {
            $this->yamlStructure['services'][$this->name]['volumes'] = [
                './www:/var/www'
            ];
            $this->yamlStructure['services'][$this->name]['working_dir'] = '/var/www';
        }

        $this->postYamlProcessing();
    }

    private function getStartupContent(): string
    {
        return <<<EOF
        #!/bin/bash

        a2enmod rewrite
        service apache2 start
        while : ; do sleep 1000; done
        EOF;
    }

    private function getDockerfile(): string
    {

        $dockerContent = "";
        switch ($this->phpVersion) {
            case "8.2":
                $dockerContent .= "FROM debian:bookworm-slim";
                break;
            case "8.4":
                $dockerContent .= "FROM php:8.4.7-apache-bookworm";
                break;
            default:
                throw new Exception("I don't have such php version. Try 8.2 or 8.4.");
        }

        $dockerContent .= <<<EOF

        
        RUN apt-get update
        RUN apt-get upgrade -y
        RUN apt-get install curl git zip -y
        RUN apt-get install php php-mysql php-xdebug php-curl php-zip php-xml php-mbstring -y
        RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer\n
        EOF;

        if ($this->node) {
            $nodePartReceipt = <<<EOF
            RUN curl -sL https://deb.nodesource.com/setup_20.x | bash - 
            RUN apt-get install -y nodejs
            
            EOF;

            $dockerContent .= $nodePartReceipt;
        }

        $dockerContent .= <<<EOF
        COPY /config/xdebug.ini /etc/php/8.2/mods-available/
        COPY /config/startup.sh /startup.sh
        COPY /config/apache2.conf /etc/apache2/
        EOF;

        if ($this->rootNameAsPublic) {
            $dockerContent .= "\nCOPY /config/000-default.conf /etc/apache2/sites-available/";
        }

        $dockerContent .= "\n";
        $dockerContent .= <<<EOF
        RUN chmod +x /startup.sh

        CMD sh /startup.sh
        EOF;

        return $dockerContent;
    }

    private function getXDebugContent(): string
    {
        return <<<EOF
        zend_extension=xdebug.so

        xdebug.start_with_request = 1
        xdebug.mode=debug
        xdebug.discover_client_host = 1
        EOF;
    }
}
