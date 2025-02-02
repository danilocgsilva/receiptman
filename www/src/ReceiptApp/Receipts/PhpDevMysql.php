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
use App\ReceiptApp\Receipts\Questions\Types\QuestionEntry;

class PhpDevMysql extends ReceiptCommons implements ReceiptInterface
{
    use HttpPortRedirection;
    use RemoveQuestionByMethod;
    private string $mysqlPortRedirection;

    private string $mysqlRootPassword;

    private bool $appDir = false;

    private bool $rootNameAsPublic = false;

    private bool $node = false;

    private bool $onDatabase = true;

    public function __construct()
    {
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
        $this->buildYamlStructure();
        
        $files = [
            new File("docker-compose.yml", Yaml::dump($this->yamlStructure, 4, 2)),
            new File("Dockerfile", $this->getDockerfile()),
            new File("config/xdebug.ini", $this->getXDebugContent()),
            new File("config/startup.sh", $this->getStartupContent())
        ];

        if ($this->appDir) {
            $files[] = new File("app/.gitkeep", "");
        } else {
            if ($this->rootNameAsPublic) {
                $files[] = new File("config/000-default.conf", $this->get000defaultFileContent());
                $files[] = new File("www/public/index.php", "<?php\necho \"Be happy!\";");
            } else {
                $files[] = new File("www/html/index.php", "<?php\necho \"Be happy!\";");
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

        $arrayPosition = array_search(
            "setMysqlPortRedirection", 
            array_map(fn (QuestionEntry $questionEntry) => $questionEntry->methodName, $this->questionsPairs),
             true
        );
        unset($this->questionsPairs[$arrayPosition]);
        $this->questionsPairs = array_values($this->questionsPairs);

        $arrayPosition = array_search(
            "setMysqlRootPassword", 
            array_map(fn (QuestionEntry $questionEntry) => $questionEntry->methodName, $this->questionsPairs),
             true
        );
        unset($this->questionsPairs[$arrayPosition]);
        $this->questionsPairs = array_values($this->questionsPairs);
        
        return $this;
    }

    public function addNode(): static
    {
        $this->node = true;
        
        return $this;
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

        service apache2 start
        while : ; do sleep 1000; done
        EOF;
    }

    private function getDockerfile(): string
    {
        $dockerContent = <<<EOF
        FROM debian:bookworm-slim

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
        COPY config/xdebug.ini /etc/php/8.2/mods-available/
        COPY config/startup.sh /startup.sh
        EOF;

        if ($this->rootNameAsPublic) {
            $dockerContent .= "\nCOPY config/000-default.conf /etc/apache2/sites-available/";
        }

        $dockerContent .= "\n";
        $dockerContent .= <<<EOF
        RUN chmod +x /startup.sh

        CMD /startup.sh
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

    private function get000defaultFileContent(): string
    {
        return <<<EOF
        <VirtualHost *:80>
            # The ServerName directive sets the request scheme, hostname and port that
            # the server uses to identify itself. This is used when creating
            # redirection URLs. In the context of virtual hosts, the ServerName
            # specifies what hostname must appear in the request's Host: header to
            # match this virtual host. For the default virtual host (this file) this
            # value is not decisive as it is used as a last resort host regardless.
            # However, you must set it for any further virtual host explicitly.
            #ServerName www.example.com

            ServerAdmin webmaster@localhost
            DocumentRoot /var/www/public

            # Available loglevels: trace8, ..., trace1, debug, info, notice, warn,
            # error, crit, alert, emerg.
            # It is also possible to configure the loglevel for particular
            # modules, e.g.
            #LogLevel info ssl:warn

            ErrorLog \${APACHE_LOG_DIR}/error.log
            CustomLog \${APACHE_LOG_DIR}/access.log combined

            # For most configuration files from conf-available/, which are
            # enabled or disabled at a global level, it is possible to
            # include a line for only one particular virtual host. For example the
            # following line enables the CGI configuration for this host only
            # after it has been globally disabled with "a2disconf".
            #Include conf-available/serve-cgi-bin.conf
        </VirtualHost>

        EOF;
    }
}
