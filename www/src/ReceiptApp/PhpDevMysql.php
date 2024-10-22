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
            new File("docker-compose.yml", Yaml::dump($this->yamlStructure, 4, 2))
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
                ],
                'environment' => [
                    sprintf('MYSQL_ROOT_PASSWORD=%s', $this->mysqlRootPassword)
                ],
                'ports' => [
                    sprintf('%s:3306', $this->mysqlPortRedirection)
                ]
            ]
        ];
    }
}
