<?php

namespace App\ReceiptApp;

use Symfony\Component\Yaml\Yaml;

class PhpDevMysql
{
    private array $yamlStructure;

    private string $name;

    private int $httpPortRedirection;

    private int $mysqlPortRedirection;

    private string $mysqlRootPassword;

    public function setMysqlPortRedirection(int $mysqlPortRedirection): static
    {
        $this->mysqlPortRedirection = $mysqlPortRedirection;
        return $this;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function setMysqlRootPassword(string $mysqRootPassword): static
    {
        $this->mysqlRootPassword = $mysqRootPassword;
        return $this;
    }

    public function setHttpPortRedirection(int $httpPortRedirection): static
    {
        $this->httpPortRedirection = $httpPortRedirection;
        return $this;
    }
    
    public function getFiles()
    {
        $this->buildYamlStructure();
        
        return [
            new File("docker-compose.yml", Yaml::dump($this->yamlStructure, 4, 2)),
            new File("Dockerfile", $this->getDockerfile()),
            new File("config/xdebug.ini", $this->getXDebugContent()),
            new File("config/startup.sh", $this->getStartupContent()),
            new File("www/.gitkeep", "")
        ];
    }

    private function buildYamlStructure()
    {
        $this->yamlStructure = [
            'services' =>[
                $this->name => [
                    'build' => [
                        'context' => '.'
                    ],
                    'container_name' => $this->name,
                    'volumes' => [
                        './www:/var/www'
                    ],
                    'ports' => [
                        sprintf('%s:80', $this->httpPortRedirection)
                    ]
                ],
                $this->name . '_db' => [
                    'image' => 'mysql:latest',
                    'container_name' => $this->name . '_db',
                    'environment' => [
                        sprintf('MYSQL_ROOT_PASSWORD=%s', $this->mysqlRootPassword)
                    ],
                    'ports' => [
                        sprintf('%s:3306', $this->mysqlPortRedirection)
                    ]
                ]
            ]
        ];
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
        return <<<EOF
FROM debian:bookworm-slim

RUN apt-get update
RUN apt-get upgrade -y
RUN apt-get install curl git zip -y
RUN apt-get install php php-mysql php-xdebug php-curl php-zip php-xml php-mbstring -y
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer
COPY config/xdebug.ini /etc/php/8.2/mods-available/
COPY config/startup.sh /startup.sh
RUN chmod +x /startup.sh

CMD /startup.sh
EOF;
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
