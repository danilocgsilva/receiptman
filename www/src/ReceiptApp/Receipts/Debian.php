<?php

namespace App\ReceiptApp\Receipts;

use App\ReceiptApp\File;
use App\ReceiptApp\ReceiptInterface;
use Symfony\Component\Yaml\Yaml;

class Debian implements ReceiptInterface
{
    private array $yamlStructure;

    private string $name;

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getFiles(): array
    {
        $this->buildYamlStructure();

        return [
            new File("docker-compose.yml", Yaml::dump($this->yamlStructure, 4, 2)),
            new File("Dockerfile", $this->getDockerfile())
        ];
    }

    public function buildYamlStructure(): void
    {
        $this->yamlStructure = [
            'services' => [
                $this->name => [
                    'build' => [
                        'context' => '.'
                    ]
                ]
            ]
        ];
    }

    private function getDockerfile(): string
    {
        return <<<EOF
FROM debian:bookworm-slim

RUN apt-get update
RUN apt-get upgrade -y

CMD while : ; do sleep 1000; done
EOF;
    }
}
