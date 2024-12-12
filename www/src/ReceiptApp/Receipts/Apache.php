<?php

declare(strict_types=1);

namespace App\ReceiptApp\Receipts;

use Symfony\Component\Yaml\Yaml;
use App\ReceiptApp\File;
use App\ReceiptApp\Receipts\Questions\BaseQuestion;
use App\ReceiptApp\Receipts\Interfaces\{
    HttpReportableInterface,
    ReceiptInterface
};
use App\ReceiptApp\Traits\HttpPortRedirection;

class Apache extends ReceiptCommons implements ReceiptInterface, HttpReportableInterface
{
    use HttpPortRedirection;
    public function getFiles(): array
    {
        $this->buildYamlStructure();

        return [
            new File("docker-compose.yml", Yaml::dump($this->yamlStructure, 4, 2))
        ];
    }

    public function buildYamlStructure(): void
    {
        $this->yamlStructure = [
            'services' => [
                $this->name => [
                    'image' => 'httpd:latest',
                    'container_name' => $this->name
                ]
            ]
        ];
    }

    public function getPropertyQuestionsPairs(): array
    {
        return (new BaseQuestion())->getPropertyQuestionPair();
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
